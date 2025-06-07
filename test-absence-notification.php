<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "Starting absence notification test...\n";

// Create a test member if not exists
$memberRole = Role::where('name', 'Member')->first();
if (!$memberRole) {
    echo "Creating Member role...\n";
    $memberRole = Role::create(['name' => 'Member']);
}

$testUser = User::where('email', 'test.member@example.com')->first();
if (!$testUser) {
    echo "Creating test member...\n";
    $testUser = User::create([
        'name' => 'Test Member',
        'email' => 'test.member@example.com',
        'password' => bcrypt('password'),
        'role_id' => $memberRole->id,
        'approval_status' => 'approved',
    ]);
}

// Create 4 Sunday events in the past
echo "Creating past Sunday events...\n";
$events = [];
for ($i = 4; $i >= 1; $i--) {
    $date = Carbon::now()->subWeeks($i)->startOfWeek()->next(Carbon::SUNDAY)->format('Y-m-d');
    $event = Event::where('date', $date)->first();
    if (!$event) {
        $event = Event::create([
            'name' => "Sunday Mass {$date}",
            'description' => "Regular Sunday Mass",
            'date' => $date,
            'time' => '08:00:00',
            'is_active' => true,
            'created_by' => 1,
        ]);
    }
    $events[] = $event;
    echo "Created event for {$date}\n";
}

// Mark the user as absent for all 4 events
echo "Marking user as absent for all events...\n";
foreach ($events as $event) {
    $attendance = Attendance::where('user_id', $testUser->id)
        ->where('event_id', $event->id)
        ->first();
    
    if (!$attendance) {
        Attendance::create([
            'user_id' => $testUser->id,
            'event_id' => $event->id,
            'status' => 'absent',
            'remarks' => 'Test absence',
        ]);
        echo "Marked absent for event on {$event->date}\n";
    } else {
        $attendance->update(['status' => 'absent']);
        echo "Updated existing attendance to absent for event on {$event->date}\n";
    }
}

// Run the check for consecutive absences
echo "\nRunning check for consecutive absences...\n";
Artisan::call('absences:check');
echo Artisan::output();

// Check if notification was created
$notification = Notification::where('user_id', $testUser->id)
    ->where('type', 'absence_warning')
    ->where('consecutive_absences', 4)
    ->first();

if ($notification) {
    echo "\nNotification created successfully:\n";
    echo "Type: {$notification->type}\n";
    echo "Message: {$notification->message}\n";
    echo "Is sent: " . ($notification->is_sent ? 'Yes' : 'No') . "\n";
} else {
    echo "\nNo notification was created for 4 consecutive absences.\n";
}

// Run the send notifications command
echo "\nRunning send notifications command...\n";
Artisan::call('notifications:send');
echo Artisan::output();

// Check if notification was marked as sent
if ($notification) {
    $notification->refresh();
    echo "\nNotification sent status: " . ($notification->is_sent ? 'Sent' : 'Not sent') . "\n";
    echo "Sent at: " . ($notification->sent_at ?? 'N/A') . "\n";
}

echo "\nTest completed.\n"; 