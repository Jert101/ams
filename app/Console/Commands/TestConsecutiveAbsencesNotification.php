<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\AbsenceWarningMail;
use App\Mail\SeriousAbsenceWarningMail;
use Illuminate\Support\Facades\Mail;

class TestConsecutiveAbsencesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absences:test-notification {email : The email to send the test notification to} {type=3 : The type of notification (3 for counseling, 4 for serious counseling)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending an absence notification email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->argument('type');
        
        // Find user by email or create a fake one for testing
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->warn("User with email {$email} not found. Creating a test user object.");
            $user = new User();
            $user->name = 'Test User';
            $user->email = $email;
        }
        
        // Create fake missed dates
        $missedDates = [
            now()->subDays(21)->toDateString(), // 3 weeks ago
            now()->subDays(14)->toDateString(), // 2 weeks ago
            now()->subDays(7)->toDateString(),  // 1 week ago
            now()->toDateString()               // today
        ];
        
        // Remove the last date if we're testing the 3-absence notification
        if ($type == 3) {
            array_pop($missedDates);
        }
        
        try {
            if ($type == 3) {
                $this->info("Sending 3-consecutive-absences notification to {$email}...");
                Mail::to($email)->send(new AbsenceWarningMail($user, $missedDates));
            } else {
                $this->info("Sending 4-consecutive-absences notification to {$email}...");
                Mail::to($email)->send(new SeriousAbsenceWarningMail($user, $missedDates));
            }
            
            $this->info('Test notification sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send test notification: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 