<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CheckConsecutiveAbsences::class,
        \App\Console\Commands\SendAbsenceNotifications::class,
        \App\Console\Commands\GenerateMemberQrCodes::class,
        \App\Console\Commands\MarkAbsences::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run the check for consecutive absences daily
        $schedule->command('absences:check')->daily();
        
        // Send email notifications for consecutive absences daily
        $schedule->command('notifications:send')->daily();
        
        // Mark absences for past events daily
        $schedule->command('attendances:mark-absences')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 