<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Services\EmailService;

echo "Starting email notification test...\n";

// Create or update test user with the specified email
$memberRole = Role::where('name', 'Member')->first();
if (!$memberRole) {
    echo "Creating Member role...\n";
    $memberRole = Role::create(['name' => 'Member']);
}

$testEmail = 'jertcatadman@gmail.com';
$testUser = User::where('email', $testEmail)->first();

if (!$testUser) {
    echo "Creating test user with email {$testEmail}...\n";
    $testUser = User::create([
        'name' => 'Jert Catadman',
        'email' => $testEmail,
        'password' => bcrypt('password'),
        'role_id' => $memberRole->id,
        'approval_status' => 'approved',
    ]);
} else {
    echo "Using existing user with email {$testEmail}...\n";
}

// Create email service
$emailService = new EmailService();

// Create absence notification message
$consecutiveAbsences = 4;
$message = "You have been absent for {$consecutiveAbsences} consecutive Sundays. This is a serious matter that requires your immediate attention.";

echo "\nSending test email notification to {$testEmail}...\n";

// Send the email
$result = $emailService->sendAbsenceNotification($testUser, $consecutiveAbsences, $message);

if ($result) {
    echo "Email sent successfully!\n";
    echo "\nEmail Content Summary:\n";
    echo "------------------------\n";
    echo "Subject: Important Notice: {$consecutiveAbsences} Consecutive Absences\n";
    echo "To: {$testUser->name} <{$testEmail}>\n";
    echo "Message: {$message}\n";
    echo "\nThe email contains HTML formatting with the KofA branding and detailed information about the consecutive absences.\n";
} else {
    echo "Failed to send email. Please check the mail configuration and logs.\n";
}

echo "\nTest completed.\n"; 