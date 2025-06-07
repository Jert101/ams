<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\MassSchedule;
use Carbon\Carbon;

class MassScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Sunday masses for the next 8 Sundays
        $this->createSundayMasses();
        
        // Create some special masses
        $this->createSpecialMasses();
    }
    
    /**
     * Create Sunday masses for the next 8 Sundays.
     */
    private function createSundayMasses(): void
    {
        $massOrders = ['first', 'second', 'third', 'fourth'];
        $massStartTimes = ['05:30:00', '07:30:00', '09:00:00', '17:00:00'];
        $massEndTimes = ['06:30:00', '08:30:00', '10:00:00', '18:00:00'];
        $attendanceStartTimes = ['05:30:00', '07:30:00', '09:00:00', '17:00:00'];
        $attendanceEndTimes = ['07:29:00', '08:59:00', '10:30:00', '18:30:00'];
        
        // Get the next 8 Sundays
        $sundays = [];
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);
        
        for ($i = 0; $i < 8; $i++) {
            $sundays[] = (clone $nextSunday)->addWeeks($i)->format('Y-m-d');
        }
        
        foreach ($sundays as $sunday) {
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
            }
        }
        
        $this->command->info('Created Sunday masses for the next 8 weeks');
    }
    
    /**
     * Create some special masses.
     */
    private function createSpecialMasses(): void
    {
        $specialMasses = [
            [
                'name' => 'Christmas Eve Mass',
                'date' => Carbon::create(2025, 12, 24)->format('Y-m-d'),
                'start_time' => '19:00:00',
                'end_time' => '20:30:00',
                'attendance_start_time' => '18:30:00',
                'attendance_end_time' => '20:00:00',
                'description' => 'Special Christmas Eve Mass',
                'location' => 'Church',
            ],
            [
                'name' => 'Easter Vigil Mass',
                'date' => Carbon::create(2025, 4, 19)->format('Y-m-d'),
                'start_time' => '20:00:00',
                'end_time' => '22:00:00',
                'attendance_start_time' => '19:30:00',
                'attendance_end_time' => '21:30:00',
                'description' => 'Easter Vigil Mass',
                'location' => 'Church',
            ],
            [
                'name' => 'Wedding Mass - Johnson Family',
                'date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'attendance_start_time' => '13:30:00',
                'attendance_end_time' => '15:00:00',
                'description' => 'Wedding Mass for the Johnson Family',
                'location' => 'Church',
            ],
        ];
        
        foreach ($specialMasses as $massData) {
            // Create event for this mass
            $event = Event::create([
                'name' => $massData['name'],
                'type' => 'special_mass',
                'date' => $massData['date'],
                'time' => $massData['start_time'],
                'end_time' => $massData['end_time'],
                'attendance_start_time' => $massData['attendance_start_time'],
                'attendance_end_time' => $massData['attendance_end_time'],
                'description' => $massData['description'],
                'location' => $massData['location'],
                'is_active' => true,
            ]);
            
            // Create mass schedule for this event
            MassSchedule::create([
                'event_id' => $event->id,
                'type' => 'special_mass',
                'mass_order' => 'special',
                'start_time' => $massData['start_time'],
                'end_time' => $massData['end_time'],
                'attendance_start_time' => $massData['attendance_start_time'],
                'attendance_end_time' => $massData['attendance_end_time'],
                'is_active' => true,
            ]);
        }
        
        $this->command->info('Created special masses');
    }
}
