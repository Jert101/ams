<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DownloadFaceApiModels;
use App\Console\Commands\QrCodeGenerator;
use App\Console\Commands\CheckFaceModels;
use App\Console\Commands\TestConsecutiveAbsencesNotification;
use App\Console\Commands\FixAdminElectionViewCommand;
use App\Console\Commands\FixElectionDatesCommand;
use App\Console\Commands\FixSyntaxErrorCommand;
use App\Console\Commands\CleanupRejectedUsers;
use App\Console\Commands\SendTestElectionEmail;

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
        \App\Console\Commands\TestConsecutiveAbsencesNotification::class,
        \App\Console\Commands\GenerateMemberQrCodes::class,
        \App\Console\Commands\MarkAbsences::class,
        \App\Console\Commands\UpdateElectionStatus::class,
        \App\Console\Commands\CheckTimezone::class,
        \App\Console\Commands\TestElectionStatusUpdate::class,
        \App\Console\Commands\ForceElectionStatusCheck::class,
        \App\Console\Commands\CheckTableSchema::class,
        \App\Console\Commands\GenerateQrCodes::class,
        \App\Console\Commands\CleanupRejectedUsers::class,
        DownloadFaceApiModels::class,
        QrCodeGenerator::class,
        CheckFaceModels::class,
        FixAdminElectionViewCommand::class,
        FixElectionDatesCommand::class,
        FixSyntaxErrorCommand::class,
        SendTestElectionEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the check for consecutive absences daily
        $schedule->command('absences:check')->daily();
        
        // Run the check for consecutive Sunday absences and send email notifications every Sunday at 10 PM
        $schedule->command('absences:check-consecutive')->weekly()->sundays()->at('22:00');
        
        // Send email notifications for consecutive absences daily
        $schedule->command('notifications:send')->daily();
        
        // Mark absences for past events daily
        $schedule->command('attendances:mark-absences')->daily();
        
        // Create Sunday masses for the next 4 weeks every Monday
        $schedule->command('masses:create-sundays')->weekly()->mondays()->at('01:00');
        
        // Update election status automatically every hour
        $schedule->command('election:update-status')->hourly();
        
        // Force election status check and broadcast every minute
        $schedule->command('election:force-check')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 