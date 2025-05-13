<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Console\Command;

class SendAbsenceNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications for consecutive absences';

    /**
     * The email service instance.
     *
     * @var EmailService
     */
    protected $emailService;

    /**
     * Create a new command instance.
     *
     * @param EmailService $emailService
     * @return void
     */
    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Sending absence notifications...');

        // Get unsent notifications for absences
        $notifications = Notification::where('is_sent', false)
            ->whereIn('type', ['absence_counseling', 'absence_warning'])
            ->with('user')
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No unsent absence notifications found.');
            return 0;
        }

        $this->info('Found ' . $notifications->count() . ' notifications to send.');
        $sentCount = 0;

        foreach ($notifications as $notification) {
            $user = $notification->user;

            // Skip if user doesn't exist or doesn't have an email
            if (!$user || !$user->email) {
                $this->warn("Skipping notification ID {$notification->id} - User not found or no email available.");
                continue;
            }

            $this->line("Sending notification to {$user->name} ({$user->email})...");

            // Send email
            $sent = $this->emailService->sendAbsenceNotification(
                $user,
                $notification->consecutive_absences,
                $notification->message
            );

            if ($sent) {
                // Mark notification as sent
                $notification->markAsSent();
                $sentCount++;
                $this->info("Successfully sent notification to {$user->name}.");
            } else {
                $this->error("Failed to send notification to {$user->name}.");
            }
        }

        $this->info("Sent {$sentCount} of {$notifications->count()} notifications.");
        return 0;
    }
} 