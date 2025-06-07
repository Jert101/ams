<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create today's event
        Event::create([
            'name' => 'Sunday Mass',
            'date' => Carbon::today()->toDateString(),
            'time' => '08:00:00',
            'description' => 'Regular Sunday mass service',
            'location' => 'Main Chapel',
            'is_active' => true,
        ]);

        // Create upcoming events
        Event::create([
            'name' => 'Monthly Meeting',
            'date' => Carbon::today()->addDays(3)->toDateString(),
            'time' => '18:30:00',
            'description' => 'Monthly KofA officer meeting',
            'location' => 'Conference Room',
            'is_active' => true,
        ]);

        Event::create([
            'name' => 'Charity Drive',
            'date' => Carbon::today()->addDays(7)->toDateString(),
            'time' => '09:00:00',
            'description' => 'Annual charity drive for the community',
            'location' => 'Community Center',
            'is_active' => true,
        ]);

        Event::create([
            'name' => 'Special Prayer Service',
            'date' => Carbon::today()->addDays(14)->toDateString(),
            'time' => '19:00:00',
            'description' => 'Special prayer service for members',
            'location' => 'Main Chapel',
            'is_active' => true,
        ]);

        Event::create([
            'name' => 'Leadership Workshop',
            'date' => Carbon::today()->addDays(21)->toDateString(),
            'time' => '10:00:00',
            'description' => 'Workshop for developing leadership skills',
            'location' => 'Training Hall',
            'is_active' => true,
        ]);
    }
}
