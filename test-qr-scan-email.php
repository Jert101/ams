<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\QrCode;
use App\Models\Event;
use App\Models\Attendance;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "Starting QR code scan and attendance confirmation email test...\n";

// Test email address
$testEmail = 'jertcatadman@gmail.com';

// Get or create a test user
$testUser = User::where('email', $testEmail)->first();
if (!$testUser) {
    echo "Creating test user with email {$testEmail}...\n";
    
    // Get the Member role
    $memberRole = DB::table('roles')->where('name', 'Member')->first();
    if (!$memberRole) {
        echo "Error: Member role not found.\n";
        exit;
    }
    
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

// Create or get QR code for the user
$qrCode = QrCode::where('user_id', $testUser->id)->first();
if (!$qrCode) {
    echo "Creating QR code for user...\n";
    $qrCode = QrCode::create([
        'user_id' => $testUser->id,
        'code' => 'TEST-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        'is_active' => true,
    ]);
}
echo "QR Code: {$qrCode->code}\n";

// Create a test event
$event = Event::create([
    'name' => 'Sunday Mass - ' . Carbon::now()->format('Y-m-d'),
    'description' => 'Regular Sunday Mass',
    'date' => Carbon::now()->format('Y-m-d'),
    'time' => '08:00:00',
    'location' => 'Church of the Knights of the Altar',
    'is_active' => true,
    'created_by' => 1,
]);
echo "Created test event: {$event->name}\n";

// Create attendance record
$attendance = Attendance::create([
    'user_id' => $testUser->id,
    'event_id' => $event->id,
    'status' => 'present',
    'approved_by' => 1,
    'approved_at' => now(),
]);
echo "Created attendance record for user\n";

// Prepare email data
$emailData = [
    'event_name' => $event->name,
    'event_date' => Carbon::parse($event->date)->format('F j, Y'),
    'event_time' => Carbon::parse($event->time)->format('g:i A'),
    'event_location' => $event->location,
    'attendance_status' => $attendance->status,
    'recorded_at' => Carbon::parse($attendance->approved_at)->format('F j, Y g:i A')
];

// Send email
echo "\nSending attendance confirmation email to {$testEmail}...\n";
$mailService = new MailService();
$result = $mailService->sendAttendanceConfirmation(
    $testUser->email,
    $testUser->name,
    $emailData
);

if ($result) {
    echo "Email sent successfully!\n";
    echo "\nEmail Content Summary:\n";
    echo "------------------------\n";
    echo "Subject: Attendance Confirmation - {$event->name}\n";
    echo "To: {$testUser->name} <{$testEmail}>\n";
    echo "Event: {$event->name}\n";
    echo "Date: " . Carbon::parse($event->date)->format('F j, Y') . "\n";
    echo "Time: " . Carbon::parse($event->time)->format('g:i A') . "\n";
    echo "Location: {$event->location}\n";
    echo "Status: " . ucfirst($attendance->status) . "\n";
    echo "Recorded at: " . Carbon::parse($attendance->approved_at)->format('F j, Y g:i A') . "\n";
    echo "\nThe email contains HTML formatting with the KofA branding and detailed information about the attendance.\n";
} else {
    echo "Failed to send email. Please check the mail configuration and logs.\n";
}

echo "\nTest completed.\n"; 