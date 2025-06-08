<?php

use App\Http\Controllers\FacialRecognitionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\MailService;

// Custom Profile Routes
Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Simple logout route for testing
Route::get('/logout-test', function() {
    Auth::logout();
    session()->flush();
    session()->regenerate();
    return redirect('/')->with('message', 'You have been logged out!');
});

// Test email route
Route::get('/test-email', function() {
    $mailService = app(MailService::class);
    $testEmail = env('MAIL_FROM_ADDRESS'); // Use the email from .env file
    $testName = 'Test User';
    
    // Test data for email
    $data = [
        'event_name' => 'Test Event',
        'event_date' => date('F j, Y'),
        'event_time' => date('g:i A'),
        'event_location' => 'Test Location',
        'attendance_status' => 'present',
        'recorded_at' => date('F j, Y g:i A')
    ];
    
    $result = $mailService->sendAttendanceConfirmation($testEmail, $testName, $data);
    
    return response()->json([
        'success' => $result,
        'message' => $result ? 'Email sent successfully!' : 'Failed to send email. Check server logs for details.'
    ]);
});

// Verification pending route
Route::get('/verification-pending', [\App\Http\Controllers\Auth\VerificationController::class, 'showPendingPage'])->name('verification.pending');

// API routes that need CSRF exemption
Route::post('/check-approval-status', [\App\Http\Controllers\Auth\VerificationController::class, 'checkStatus'])
    ->name('verification.check-status')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/check-user-exists', [\App\Http\Controllers\Auth\VerificationController::class, 'checkUserExists'])
    ->name('verification.check-user-exists')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', function () {
    // Log auth state for debugging
    \Log::info('Auth check: ' . (Auth::check() ? 'true' : 'false'));
    \Log::info('Session: ' . json_encode(session()->all()));
    
    // Return the index view
    return view('index');
});

// Main dashboard route - redirects to appropriate dashboard based on user role
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if (!$user) {
        return redirect('/');
    }
    
    // Get the user's role
    $role = $user->role->name ?? null;
    
    // Redirect based on role
    switch ($role) {
        case 'Admin':
            return redirect()->route('admin.dashboard');
        case 'Officer':
            return redirect()->route('officer.dashboard');
        case 'Secretary':
            return redirect()->route('secretary.dashboard');
        case 'Member':
            return redirect()->route('member.dashboard');
        default:
            return redirect('/');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Events Calendar
Route::get('/events/calendar', function () {
    return view('events.calendar');
})->middleware(['auth', 'verified'])->name('events.calendar');

// React Example Page
Route::get('/react-example', function () {
    return view('react-example');
})->middleware(['auth', 'verified'])->name('react.example');

// Attendance Scanner
Route::get('/attendance/scan', function () {
    return view('attendance.scan');
})->middleware(['auth', 'verified'])->name('attendance.scan');

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        // Get statistics for the admin dashboard
        $totalUsers = \App\Models\User::count();
        $totalEvents = \App\Models\Event::count();
        $totalAttendances = \App\Models\Attendance::count();
        $totalNotifications = \App\Models\Notification::count();
        
        // Get recent users for display with approval status
        $recentUsers = \App\Models\User::with('role')->latest()->take(5)->get();
        
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
    })->name('dashboard');
    
    // User Management Routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // API routes for React components
    Route::get('/users/api', [\App\Http\Controllers\Api\ReactController::class, 'getUserList']);
    
    // React version of the users index page
    Route::get('/users/react', function() {
        return view('admin.users.react-index');
    })->name('users.react');
    
    // Role Management Routes
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    
    // Event Management Routes
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    
    // Mass Schedule Management Routes
    Route::resource('mass-schedules', \App\Http\Controllers\Admin\MassScheduleController::class);
    Route::post('mass-schedules/setup-sunday', [\App\Http\Controllers\Admin\MassScheduleController::class, 'setupSundayMasses'])->name('mass-schedules.setup-sunday');
    
    // System Settings Routes
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
});

// Admin registration approval routes
Route::prefix('admin')->middleware(['auth', 'verified', 'role:Admin'])->name('admin.')->group(function () {
    // User registration approvals
    Route::get('/approvals', [\App\Http\Controllers\Admin\UserApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/{user}', [\App\Http\Controllers\Admin\UserApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{user}/approve', [\App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [\App\Http\Controllers\Admin\UserApprovalController::class, 'reject'])->name('approvals.reject');
});

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
    
    // Get upcoming events for display
    $upcomingEvents = \App\Models\Event::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->take(5)
        ->get();
    
    return view('officer.dashboard', compact(
        'todayEvent',
        'todayAttendanceCount',
        'todayAbsentCount',
        'todayExcusedCount',
        'recentAttendances',
        'upcomingEvents'
    ));
})->middleware(['auth', 'verified'])->name('officer.dashboard');

// Officer QR Code Scanner routes
Route::middleware(['auth', 'verified', 'role:Officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/scan', [\App\Http\Controllers\Officer\ScanController::class, 'index'])->name('scan');
    Route::post('/scan/process', [\App\Http\Controllers\Officer\ScanController::class, 'process'])->name('scan.process');
    Route::post('/quick-mass', [\App\Http\Controllers\Officer\ScanController::class, 'quickMassStore'])->name('quick-mass.store');
    Route::post('/scan/process-face', [\App\Http\Controllers\Officer\ScanController::class, 'processFaceRecognition'])->name('scan.process-face');
});

// Officer Events routes
Route::middleware(['auth', 'verified'])->prefix('officer')->name('officer.')->group(function () {
    Route::resource('events', \App\Http\Controllers\Officer\EventController::class);
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
    Route::post('/notifications/send', [\App\Http\Controllers\Secretary\NotificationController::class, 'send'])->name('notifications.send');
    Route::post('/notifications/send-to-all', [\App\Http\Controllers\Secretary\NotificationController::class, 'sendToAll'])->name('notifications.send-to-all');
    Route::post('/notifications/{notification}/mark-as-sent', [\App\Http\Controllers\Secretary\NotificationController::class, 'markAsSent'])->name('notifications.mark-as-sent');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Secretary\NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Reports management
    Route::get('/reports', [\App\Http\Controllers\Secretary\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/by-date-range', [\App\Http\Controllers\Secretary\ReportController::class, 'byDateRange'])->name('reports.by-date-range');
    Route::get('/reports/by-member', [\App\Http\Controllers\Secretary\ReportController::class, 'byMember'])->name('reports.by-member');
    Route::get('/reports/export-csv', [\App\Http\Controllers\Secretary\ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export-three-consecutive-absences', [\App\Http\Controllers\Secretary\ReportController::class, 'exportThreeConsecutiveAbsences'])->name('reports.export-three-consecutive-absences');
    Route::get('/reports/export-four-plus-consecutive-absences', [\App\Http\Controllers\Secretary\ReportController::class, 'exportFourPlusConsecutiveAbsences'])->name('reports.export-four-plus-consecutive-absences');
    
    // PDF Exports
    Route::get('/reports/export-three-consecutive-absences-pdf', [\App\Http\Controllers\Secretary\ReportController::class, 'exportThreeConsecutiveAbsencesPdf'])->name('reports.export-three-consecutive-absences-pdf');
    Route::get('/reports/export-four-plus-consecutive-absences-pdf', [\App\Http\Controllers\Secretary\ReportController::class, 'exportFourPlusConsecutiveAbsencesPdf'])->name('reports.export-four-plus-consecutive-absences-pdf');
    
    // Members management
    Route::get('/members', [\App\Http\Controllers\Secretary\MemberController::class, 'index'])->name('members.index');
});

// Member Dashboard
Route::middleware(['auth', 'verified', 'approved'])->get('/member/dashboard', function () {
    // Get the authenticated user
    $user = auth()->user();
    
    // Get attendance statistics for the member
    $presentCount = \App\Models\Attendance::where('user_id', $user->user_id)
        ->where('status', 'present')
        ->count();
        
    $absentCount = \App\Models\Attendance::where('user_id', $user->user_id)
        ->where('status', 'absent')
        ->count();
        
    $excusedCount = \App\Models\Attendance::where('user_id', $user->user_id)
        ->where('status', 'excused')
        ->count();
    
    // Calculate attendance rate
    $totalAttendances = $presentCount + $absentCount + $excusedCount;
    $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;
    
    // Get the next upcoming event
    $nextEvent = \App\Models\Event::where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->first();
    
    // Get recent attendance history
    $recentAttendances = \App\Models\Attendance::with('event')
        ->where('user_id', $user->user_id)
        ->latest()
        ->take(10)
        ->get();
    
    return view('member.dashboard', compact(
        'presentCount',
        'absentCount',
        'excusedCount',
        'attendanceRate',
        'nextEvent',
        'recentAttendances'
    ));
})->middleware(['auth', 'verified'])->name('member.dashboard');

// Member Attendance routes
Route::middleware(['auth', 'verified', 'approved', 'role:Member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/attendances', [\App\Http\Controllers\Member\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/create', [\App\Http\Controllers\Member\AttendanceController::class, 'create'])->name('attendances.create');
    Route::get('/attendances/monthly', [\App\Http\Controllers\Member\AttendanceController::class, 'monthly'])->name('attendances.monthly');
    Route::get('/attendances/{attendance}', [\App\Http\Controllers\Member\AttendanceController::class, 'show'])->name('attendances.show');
    Route::post('/attendances', [\App\Http\Controllers\Member\AttendanceController::class, 'store'])->name('attendances.store');
});

// Officer Attendance routes
Route::middleware(['auth', 'verified', 'role:Officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/attendances', [\App\Http\Controllers\Officer\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/edit/{attendance}', [\App\Http\Controllers\Officer\AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}', [\App\Http\Controllers\Officer\AttendanceController::class, 'update'])->name('attendances.update');
    Route::post('/attendances/mark-present', [\App\Http\Controllers\Officer\AttendanceController::class, 'markPresent'])->name('attendances.mark-present');
    Route::post('/attendances/mark-absent', [\App\Http\Controllers\Officer\AttendanceController::class, 'markAbsent'])->name('attendances.mark-absent');
    Route::get('/attendances/pending', [\App\Http\Controllers\Officer\AttendanceController::class, 'pending'])->name('attendances.pending');
    Route::get('/attendances/verify/{attendance}', [\App\Http\Controllers\Officer\AttendanceController::class, 'verify'])->name('attendances.verify');
    Route::post('/attendances/verify/{attendance}', [\App\Http\Controllers\Officer\AttendanceController::class, 'processVerification'])->name('attendances.process-verification');
});

// Member Profile route
Route::get('/member/profile', function() {
    return view('member.profile');
})->middleware(['auth', 'verified', 'approved', 'role:Member'])->name('member.profile');

// Election routes (accessible to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/election', [App\Http\Controllers\ElectionController::class, 'index'])->name('election.index');
    Route::post('/election/apply', [App\Http\Controllers\ElectionController::class, 'applyForCandidacy'])->name('election.apply');
    Route::post('/election/vote', [App\Http\Controllers\ElectionController::class, 'vote'])->name('election.vote');
    Route::get('/election/candidate/{candidate}', [App\Http\Controllers\ElectionController::class, 'viewCandidate'])->name('election.candidate');
});

// Admin election routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/election', [App\Http\Controllers\Admin\ElectionController::class, 'index'])->name('election.index');
    Route::post('/election/settings', [App\Http\Controllers\Admin\ElectionController::class, 'updateSettings'])->name('election.settings');
    Route::post('/election/status', [App\Http\Controllers\Admin\ElectionController::class, 'changeStatus'])->name('election.change-status');
    Route::post('/election/position', [App\Http\Controllers\Admin\ElectionController::class, 'storePosition'])->name('election.position.store');
    Route::put('/election/position/{position}', [App\Http\Controllers\Admin\ElectionController::class, 'updatePosition'])->name('election.position.update');
    Route::delete('/election/position/{position}', [App\Http\Controllers\Admin\ElectionController::class, 'deletePosition'])->name('election.position.delete');
    Route::get('/election/results', [App\Http\Controllers\Admin\ElectionController::class, 'results'])->name('election.results');
    Route::get('/election/archives', [App\Http\Controllers\Admin\ElectionController::class, 'archives'])->name('election.archives');
    Route::get('/election/archives/{archive}', [App\Http\Controllers\Admin\ElectionController::class, 'viewArchive'])->name('election.archive');
    Route::post('/election/send-notifications', [App\Http\Controllers\Admin\ElectionController::class, 'sendWinnerNotifications'])->name('election.send-notifications');
    
    // Candidate management
    Route::get('/election/candidates', [App\Http\Controllers\Admin\ElectionController::class, 'listCandidates'])->name('election.candidates');
    Route::get('/election/view-candidate/{id}', [App\Http\Controllers\Admin\ElectionController::class, 'viewCandidate'])->name('election.candidate');
    Route::post('/election/approve-candidate/{id}', [App\Http\Controllers\Admin\ElectionController::class, 'approveCandidate'])->name('election.approve-candidate');
    Route::post('/election/reject-candidate/{id}', [App\Http\Controllers\Admin\ElectionController::class, 'rejectCandidate'])->name('election.reject-candidate');
    Route::post('/election/toggle-auto-approval', [App\Http\Controllers\Admin\ElectionController::class, 'toggleAutoApproval'])->name('election.toggle-auto-approval');
    
    // Test route for debugging
    Route::get('/election/test-add-position', [App\Http\Controllers\Admin\ElectionController::class, 'testAddPosition'])->name('election.test-add-position');
});

// Add the following route for the memory bank management page
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/memory-bank', function () {
        return view('admin.memory-bank');
    })->name('admin.memory-bank');
});

// QR Code routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Allow viewing QR code for all authenticated users
    Route::get('/qrcode', [\App\Http\Controllers\QrCodeController::class, 'show'])->name('qrcode.show');
    
    // Admin-only QR code routes
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/qrcode/print', [\App\Http\Controllers\QrCodeController::class, 'printCard'])->name('qrcode.print');
        Route::match(['get', 'post'], '/admin/qrcode/batch-print', [\App\Http\Controllers\QrCodeController::class, 'printBatch'])->name('qrcode.print-batch');
        Route::post('/admin/qrcode/regenerate/{userId}', [\App\Http\Controllers\QrCodeController::class, 'regenerate'])->name('qrcode.regenerate');
        
        // New routes for admin to manage user QR codes
        Route::get('/admin/qrcode/manage', [\App\Http\Controllers\QrCodeController::class, 'manageQrCodes'])->name('admin.qrcode.manage');
        Route::get('/admin/qrcode/view/{userId}', [\App\Http\Controllers\QrCodeController::class, 'viewUserQrCode'])->name('admin.qrcode.view');
        Route::get('/admin/qrcode/print/{userId}', [\App\Http\Controllers\QrCodeController::class, 'printUserQrCode'])->name('admin.qrcode.print');
    });
});

// QR Code Routes
Route::middleware(['auth', 'verified'])->prefix('qrcode')->name('qrcode.')->group(function () {
    Route::get('/view', [\App\Http\Controllers\QrCodeController::class, 'show'])->name('view');
    Route::post('/generate', [\App\Http\Controllers\QrCodeController::class, 'generate'])->name('generate');
    Route::get('/print', [\App\Http\Controllers\QrCodeController::class, 'printCard'])->name('print');
    
    // Debug route - admin only
    Route::get('/debug/{userId}', [\App\Http\Controllers\QrCodeController::class, 'debugUserData'])
        ->middleware('role:Admin')
        ->name('debug');
    Route::get('/test/{userId}', [\App\Http\Controllers\QrCodeController::class, 'testSimpleView'])
        ->middleware('role:Admin')
        ->name('test');
});

// Admin QR Code Management Routes
Route::middleware(['auth', 'verified', 'role:Admin'])->prefix('admin/qrcode')->name('admin.qrcode.')->group(function () {
    Route::get('/manage', [\App\Http\Controllers\QrCodeController::class, 'manageQrCodes'])->name('manage');
    Route::get('/view/{userId}', [\App\Http\Controllers\QrCodeController::class, 'viewUserQrCode'])->name('view');
    Route::get('/print/{userId}', [\App\Http\Controllers\QrCodeController::class, 'printUserQrCode'])->name('print');
    Route::post('/regenerate/{userId}', [\App\Http\Controllers\QrCodeController::class, 'regenerate'])->name('regenerate');
    Route::get('/batch', [\App\Http\Controllers\QrCodeController::class, 'printBatch'])->name('batch');
    Route::post('/batch', [\App\Http\Controllers\QrCodeController::class, 'printBatch'])->name('batch.print');
    Route::get('/debug/{userId}', [\App\Http\Controllers\QrCodeController::class, 'debugUserData'])->name('debug');
});

// Debug route for checking approval status (only in local environment)
if (app()->environment('local')) {
    Route::get('/debug-user-status/{email}', function ($email) {
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
        
        return response()->json([
            'user_id' => $user->user_id,
            'email' => $user->email,
            'approval_status' => $user->approval_status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ]);
    });
}

require __DIR__.'/auth.php';
