<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckConsecutiveAbsences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absences:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for members with consecutive absences';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for consecutive absences...');

        // Get events that happened in the last 1 month, ordered by date
        $events = Event::where('date', '>=', now()->subMonth())
            ->orderBy('date')
            ->get();

        $eventIds = $events->pluck('id')->toArray();

        if (empty($eventIds)) {
            $this->warn('No events found in the last month.');
            return 0;
        }

        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })->get();

        $this->info('Processing ' . $members->count() . ' members...');

        $notificationCount = 0;

        foreach ($members as $member) {
            // Get the member's attendance records for these events
            $attendances = Attendance::where('user_id', $member->id)
                ->whereIn('event_id', $eventIds)
                ->orderBy('created_at')
                ->get()
                ->keyBy('event_id');

            // Check for consecutive Sundays with absences
            $consecutiveAbsences = $this->calculateConsecutiveAbsences($member, $events, $attendances);

            // Create notifications for members with 3 or 4 consecutive absences
            if ($consecutiveAbsences === 3) {
                $this->createAbsenceNotification($member, $consecutiveAbsences, 'counseling');
                $notificationCount++;
            } elseif ($consecutiveAbsences === 4) {
                $this->createAbsenceNotification($member, $consecutiveAbsences, 'warning');
                $notificationCount++;
            }
        }

        $this->info('Created ' . $notificationCount . ' notifications for members with consecutive absences.');
        return 0;
    }

    /**
     * Calculate the number of consecutive absences for a member.
     *
     * @param User $member
     * @param Collection $events
     * @param Collection $attendances
     * @return int
     */
    private function calculateConsecutiveAbsences($member, $events, $attendances)
    {
        $consecutiveCount = 0;
        $maxConsecutive = 0;

        foreach ($events as $event) {
            // Sunday is represented by 0 in PHP's date('w')
            $isSunday = date('w', strtotime($event->date)) === '0';

            if (!$isSunday) {
                continue; // Skip non-Sunday events
            }

            $attendance = $attendances->get($event->id);

            // If no attendance record exists or status is absent
            if (!$attendance || $attendance->status === 'absent') {
                $consecutiveCount++;
                // Update the maximum consecutive count
                $maxConsecutive = max($maxConsecutive, $consecutiveCount);
            } else {
                // Reset the consecutive count if present or excused
                $consecutiveCount = 0;
            }
        }

        return $maxConsecutive;
    }

    /**
     * Create an absence notification for a member.
     *
     * @param User $member
     * @param int $consecutiveAbsences
     * @param string $type
     * @return void
     */
    private function createAbsenceNotification($member, $consecutiveAbsences, $type)
    {
        $message = "You have been absent for {$consecutiveAbsences} consecutive Sundays. ";
        
        if ($type === 'counseling') {
            $message .= "Please note that you need to undergo counseling as per organization rules.";
        } else {
            $message .= "This is a serious matter that requires your immediate attention.";
        }

        Notification::create([
            'user_id' => $member->id,
            'type' => 'absence_' . $type,
            'message' => $message,
            'is_sent' => false,
            'consecutive_absences' => $consecutiveAbsences,
        ]);

        $this->line("Created notification for {$member->name} with {$consecutiveAbsences} consecutive absences.");
    }
} 