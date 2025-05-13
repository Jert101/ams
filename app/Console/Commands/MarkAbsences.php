<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Console\Command;
use Carbon\Carbon;

class MarkAbsences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendances:mark-absences {--days=1 : Number of days in the past to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark absences for events that have passed without attendance records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $daysAgo = $this->option('days');
        $this->info("Marking absences for events from the past {$daysAgo} days...");
        
        // Get past events that need processing
        $pastDate = Carbon::now()->subDays($daysAgo)->format('Y-m-d');
        $events = Event::whereDate('date', '<=', $pastDate)
            ->where('date', '>=', Carbon::now()->subDays(30)->format('Y-m-d')) // Only process last 30 days
            ->get();
            
        if ($events->isEmpty()) {
            $this->info('No past events found to process.');
            return 0;
        }
        
        $this->info("Found " . $events->count() . " events to process.");
        
        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })->get();
        
        $this->info("Processing " . $members->count() . " members...");
        
        $markedCount = 0;
        $eventIds = $events->pluck('id')->toArray();
        
        // Get existing attendance records
        $existingAttendances = Attendance::whereIn('event_id', $eventIds)
            ->get()
            ->groupBy(['user_id', 'event_id']);
        
        // Loop through each member
        foreach ($members as $member) {
            $this->line("Processing member: {$member->name}");
            
            // Loop through each event
            foreach ($events as $event) {
                // Check if an attendance record already exists
                if (isset($existingAttendances[$member->id][$event->id])) {
                    $this->line("  Attendance record already exists for event: {$event->name}");
                    continue;
                }
                
                // Create a new absence record
                Attendance::create([
                    'user_id' => $member->id,
                    'event_id' => $event->id,
                    'status' => 'absent',
                    'approved_by' => null,
                    'approved_at' => null,
                    'remarks' => 'Automatically marked as absent',
                ]);
                
                $markedCount++;
                $this->line("  Marked absent for event: {$event->name}");
            }
        }
        
        $this->info("Successfully marked {$markedCount} absences.");
        return 0;
    }
} 