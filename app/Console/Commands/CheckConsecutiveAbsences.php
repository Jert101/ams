<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbsenceWarningMail;
use App\Mail\SeriousAbsenceWarningMail;
use Carbon\Carbon;

class CheckConsecutiveAbsences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absences:check-consecutive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users with consecutive Sunday absences and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for consecutive Sunday absences...');
        
        // Get the recent Sundays (dates)
        $recentSundayDates = Event::where(function ($query) {
                $query->where('type', 'sunday')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                            ->orWhere('name', 'like', '%Sunday%');
                    });
            })
            ->orderBy('date', 'desc')
            ->distinct()
            ->pluck('date')
            ->take(5)
            ->toArray();
            
        // Get all members
        $members = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            })->get();
            
        $this->info('Found ' . count($members) . ' members to check.');
        
        $notificationCount = 0;
        
        // Check each member for consecutive Sunday absences
        foreach ($members as $member) {
            $sundayAbsenceCount = 0;
            $consecutiveSundays = [];
            
            foreach ($recentSundayDates as $date) {
                // Get all events for this Sunday
                $sundayEvents = Event::where('date', $date)->pluck('id')->toArray();
                
                if (empty($sundayEvents)) {
                    continue;
                }
                
                // Check if member attended any of the masses on this Sunday
                $attendedCount = Attendance::where('user_id', $member->id)
                    ->whereIn('event_id', $sundayEvents)
                    ->where('status', 'present')
                    ->count();
                
                // If they didn't attend any masses on this Sunday, count as absent
                if ($attendedCount === 0) {
                    $sundayAbsenceCount++;
                    $consecutiveSundays[] = $date;
                } else {
                    // Break the consecutive chain if they attended a mass
                    break;
                }
            }
            
            // Only process if they have 3 or more consecutive absences
            if ($sundayAbsenceCount >= 3) {
                // Check if we've already sent a notification for this number of absences
                $existingNotification = Notification::where('user_id', $member->id)
                    ->where('type', 'absence_warning')
                    ->where('consecutive_absences', $sundayAbsenceCount)
                    ->where('created_at', '>=', Carbon::now()->subDays(7)) // Only check the last week
                    ->exists();
                    
                if (!$existingNotification) {
                    // Create a notification record
                    $notification = new Notification();
                    $notification->user_id = $member->id;
                    $notification->type = 'absence_warning';
                    $notification->consecutive_absences = $sundayAbsenceCount;
                    
                    if ($sundayAbsenceCount == 3) {
                        $notification->message = "Dear {$member->name}, we've noticed you've been absent from Sunday masses for 3 consecutive weeks. Each Sunday has 4 masses (please attend at least one of them to be marked as present). You will need to undergo counseling at the next meeting. Please let us know if you need any assistance.";
                        
                        // Send the email for 3 consecutive absences
                        $this->sendAbsenceWarningEmail($member, $consecutiveSundays);
                    } else if ($sundayAbsenceCount >= 4) {
                        $notification->message = "Dear {$member->name}, we've noticed you've been absent from Sunday masses for 4 or more consecutive weeks. Each Sunday has 4 masses (please attend at least one of them to be marked as present). You will need to undergo serious counseling on the next Sunday. If you fail to attend, the council will visit you at your home. Please contact us immediately.";
                        
                        // Send the email for 4+ consecutive absences
                        $this->sendSeriousAbsenceWarningEmail($member, $consecutiveSundays);
                    }
                    
                    $notification->is_sent = true;
                    $notification->sent_at = now();
                    $notification->save();
                    
                    $notificationCount++;
                    
                    $this->info("Sent notification to {$member->name} for {$sundayAbsenceCount} consecutive absences.");
                }
            }
        }
        
        $this->info("Sent a total of {$notificationCount} notifications.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Send an email notification for 3 consecutive absences.
     */
    private function sendAbsenceWarningEmail($user, $missedDates)
    {
        try {
            Mail::to($user->email)->send(new AbsenceWarningMail($user, $missedDates));
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$user->name}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send an email notification for 4+ consecutive absences.
     */
    private function sendSeriousAbsenceWarningEmail($user, $missedDates)
    {
        try {
            Mail::to($user->email)->send(new SeriousAbsenceWarningMail($user, $missedDates));
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$user->name}: " . $e->getMessage());
            return false;
        }
    }
} 