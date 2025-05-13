<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// Default dashboard (fallback)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin dashboard
Route::get('/admin/dashboard', function () {
    // Get statistics for the admin dashboard
    $totalUsers = \App\Models\User::count();
    $totalEvents = \App\Models\Event::count();
    $totalAttendances = \App\Models\Attendance::count();
    $totalNotifications = \App\Models\Notification::count();
    
    // Get recent users for display
    $recentUsers = \App\Models\User::latest()->take(5)->get();
    
    // Get recent events for display
    $recentEvents = \App\Models\Event::latest()->take(5)->get();
    
    // Get upcoming events for display
    $upcomingEvents = \App\Models\Event::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->take(5)
        ->get();
    
    // Get recent attendances for display
    $recentAttendances = \App\Models\Attendance::with(['user', 'event'])
        ->latest()
        ->take(10)
        ->get();
    
    return view('admin.dashboard', compact(
        'totalUsers', 
        'totalEvents', 
        'totalAttendances', 
        'totalNotifications',
        'recentUsers',
        'recentEvents',
        'upcomingEvents',
        'recentAttendances'
    ));
})->middleware(['auth', 'verified'])->name('admin.dashboard');

// Officer dashboard
Route::get('/officer/dashboard', function () {
    // Get today's event if any
    $todayEvent = \App\Models\Event::whereDate('date', now()->toDateString())->first();
    
    // Get today's attendance stats
    $todayAttendanceCount = 0;
    $todayAbsentCount = 0;
    $todayExcusedCount = 0;
    
    if ($todayEvent) {
        $todayAttendanceCount = \App\Models\Attendance::where('event_id', $todayEvent->id)
            ->where('status', 'present')->count();
        $todayAbsentCount = \App\Models\Attendance::where('event_id', $todayEvent->id)
            ->where('status', 'absent')->count();
        $todayExcusedCount = \App\Models\Attendance::where('event_id', $todayEvent->id)
            ->where('status', 'excused')->count();
    }
    
    // Get recent activity
    $recentAttendances = \App\Models\Attendance::with(['user', 'event'])
        ->latest()
        ->take(10)
        ->get();
    
    return view('officer.dashboard', compact(
        'todayEvent',
        'todayAttendanceCount',
        'todayAbsentCount',
        'todayExcusedCount',
        'recentAttendances'
    ));
})->middleware(['auth', 'verified'])->name('officer.dashboard');

// Officer QR Code Scanner routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/officer/scan', [\App\Http\Controllers\Officer\ScanController::class, 'index'])->name('officer.scan');
    Route::post('/officer/scan/process', [\App\Http\Controllers\Officer\ScanController::class, 'process'])->name('officer.scan.process');
});

// Secretary dashboard
Route::get('/secretary/dashboard', function () {
    // Get attendance statistics
    $totalMembers = \App\Models\User::where('role_id', function($query) {
        $query->select('id')
            ->from('roles')
            ->where('name', 'Member');
    })->count();
    
    $totalEvents = \App\Models\Event::count();
    
    // Calculate average attendance
    $avgAttendance = 0;
    if ($totalEvents > 0) {
        $presentAttendances = \App\Models\Attendance::where('status', 'present')->count();
        $avgAttendance = round(($presentAttendances / ($totalEvents * $totalMembers)) * 100, 1);
    }
    
    // Get recent notifications
    $recentNotifications = \App\Models\Notification::with('user')
        ->latest()
        ->take(5)
        ->get();
    
    // Get members with consecutive absences (3 or more)
    $membersWithConsecutiveAbsences = \App\Models\User::whereHas('attendances', function($query) {
        $query->where('status', 'absent');
    }, '>=', 3)->with(['attendances' => function($query) {
        $query->where('status', 'absent')
            ->latest()
            ->take(3);
    }])->take(10)->get();
    
    return view('secretary.dashboard', compact(
        'totalMembers',
        'totalEvents',
        'avgAttendance',
        'recentNotifications',
        'membersWithConsecutiveAbsences'
    ));
})->middleware(['auth', 'verified'])->name('secretary.dashboard');

// Secretary routes
Route::middleware(['auth', 'verified'])->prefix('secretary')->name('secretary.')->group(function () {
    // Absences management
    Route::get('/absences', [\App\Http\Controllers\Secretary\AbsenceController::class, 'index'])->name('absences.index');
    
    // Notifications management
    Route::get('/notifications', [\App\Http\Controllers\Secretary\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [\App\Http\Controllers\Secretary\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\Secretary\NotificationController::class, 'store'])->name('notifications.store');
    
    // Reports management
    Route::get('/reports', [\App\Http\Controllers\Secretary\ReportController::class, 'index'])->name('reports.index');
    
    // Members management
    Route::get('/members', [\App\Http\Controllers\Secretary\MemberController::class, 'index'])->name('members.index');
});

// Member dashboard
Route::get('/member/dashboard', function () {
    // Get the authenticated user
    $user = auth()->user();
    
    // Get attendance statistics for the member
    $presentCount = \App\Models\Attendance::where('user_id', $user->id)
        ->where('status', 'present')
        ->count();
        
    $absentCount = \App\Models\Attendance::where('user_id', $user->id)
        ->where('status', 'absent')
        ->count();
        
    $excusedCount = \App\Models\Attendance::where('user_id', $user->id)
        ->where('status', 'excused')
        ->count();
    
    // Calculate attendance rate
    $totalAttendances = $presentCount + $absentCount + $excusedCount;
    $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;
    
    // Get the next upcoming event
    $nextEvent = \App\Models\Event::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->first();
    
    // Get the member's QR code
    $qrCode = $user->qrCode;
    
    // Get recent attendance history
    $recentAttendances = \App\Models\Attendance::with('event')
        ->where('user_id', $user->id)
        ->latest()
        ->take(10)
        ->get();
    
    return view('member.dashboard', compact(
        'presentCount',
        'absentCount',
        'excusedCount',
        'attendanceRate',
        'nextEvent',
        'qrCode',
        'recentAttendances'
    ));
})->middleware(['auth', 'verified'])->name('member.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Member QR Code route
    Route::get('/member/qrcode', [\App\Http\Controllers\Member\QrCodeController::class, 'show'])->middleware('role:Member')->name('qrcode.show');
});

require __DIR__.'/auth.php';
