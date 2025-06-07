<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\MassSchedule;
use Carbon\Carbon;

class CreateSundayMasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masses:create-sundays {weeks=4 : Number of weeks ahead to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Sunday masses for future weeks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $weeksAhead = $this->argument('weeks');
        
        $this->info("Creating Sunday masses for the next {$weeksAhead} weeks...");
        
        $massOrders = ['first', 'second', 'third', 'fourth'];
        $massStartTimes = ['05:30:00', '07:30:00', '09:00:00', '17:00:00'];
        $massEndTimes = ['06:30:00', '08:30:00', '10:00:00', '18:00:00'];
        $attendanceStartTimes = ['05:30:00', '07:30:00', '09:00:00', '17:00:00'];
        $attendanceEndTimes = ['07:29:00', '08:59:00', '10:30:00', '18:30:00'];
        
        // Get the last Sunday we have in the database
        $lastSundayEvent = Event::where('type', 'sunday_mass')
            ->orderBy('date', 'desc')
            ->first();
            
        // If we have no Sunday masses yet, start from next Sunday
        $startDate = $lastSundayEvent 
            ? Carbon::parse($lastSundayEvent->date)->next(Carbon::SUNDAY)
            : Carbon::now()->next(Carbon::SUNDAY);
        
        $bar = $this->output->createProgressBar($weeksAhead * count($massOrders));
        $bar->start();
        
        $createdCount = 0;
        
        // Create masses for each Sunday
        for ($week = 0; $week < $weeksAhead; $week++) {
            $sunday = (clone $startDate)->addWeeks($week)->format('Y-m-d');
            
            // Check if we already have masses for this Sunday
            $existingMass = Event::where('date', $sunday)
                ->where('type', 'sunday_mass')
                ->first();
                
            if ($existingMass) {
                $this->warn("Masses for {$sunday} already exist, skipping.");
                continue;
            }
            
            for ($i = 0; $i < count($massOrders); $i++) {
                // Create event for this mass
                $event = Event::create([
                    'name' => ucfirst($massOrders[$i]) . ' Sunday Mass',
                    'type' => 'sunday_mass',
                    'mass_order' => $massOrders[$i],
                    'date' => $sunday,
                    'time' => $massStartTimes[$i],
                    'end_time' => $massEndTimes[$i],
                    'attendance_start_time' => $attendanceStartTimes[$i],
                    'attendance_end_time' => $attendanceEndTimes[$i],
                    'description' => 'Regular Sunday Mass',
                    'location' => 'Church',
                    'is_active' => true,
                ]);
                
                // Create mass schedule for this event
                MassSchedule::create([
                    'event_id' => $event->id,
                    'type' => 'sunday_mass',
                    'mass_order' => $massOrders[$i],
                    'start_time' => $massStartTimes[$i],
                    'end_time' => $massEndTimes[$i],
                    'attendance_start_time' => $attendanceStartTimes[$i],
                    'attendance_end_time' => $attendanceEndTimes[$i],
                    'is_active' => true,
                ]);
                
                $createdCount++;
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Created {$createdCount} Sunday masses successfully!");
        
        return Command::SUCCESS;
    }
}
