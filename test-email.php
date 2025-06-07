<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\MailService;

// Print mail configuration
echo "Mail Configuration:\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? '[SET]' : '[NOT SET]') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n\n";

// Create an instance of the mail service
$mailService = app(MailService::class);

// Test data for email
$data = [
    'event_name' => 'Test Event',
    'event_date' => date('F j, Y'),
    'event_time' => date('g:i A'),
    'event_location' => 'Test Location',
    'attendance_status' => 'present',
    'recorded_at' => date('F j, Y g:i A')
];

// Attempt to send the email
echo "Attempting to send test email...\n";
$result = $mailService->sendAttendanceConfirmation(
    env('MAIL_FROM_ADDRESS'),
    'Test User',
    $data
);

// Output the result
echo "\nEmail sending " . ($result ? "successful!" : "failed!") . "\n";

if (!$result) {
    echo "Check your mail configuration in .env file.\n";
    echo "For Gmail, make sure you:\n";
    echo "1. Have the correct password (app password if 2FA is enabled)\n";
    echo "2. Have allowed less secure apps if not using 2FA\n";
}
