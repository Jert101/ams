<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get counts for dashboard stats
        $totalUsers = Cache::remember('dashboard_total_users', 300, function() {
            return User::count();
        });
        $totalEvents = Cache::remember('dashboard_total_events', 300, function() {
            return Event::count();
        });
        $totalAttendances = Cache::remember('dashboard_total_attendances', 300, function() {
            return Attendance::count();
        });
        $totalNotifications = Cache::remember('dashboard_total_notifications', 300, function() {
            return Notification::count();
        });
        $pendingRegistrations = Cache::remember('dashboard_pending_registrations', 300, function() {
            return User::where('approval_status', 'pending')->count();
        });
        
        // Get recent users with eager loading to avoid N+1 queries
        $recentUsers = Cache::remember('dashboard_recent_users', 300, function() {
            return User::with(['role'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($user) {
                    // Add profile_photo_url to the array
                    $userData = $user->toArray();
                    return $userData;
                });
        });
            
        // Get upcoming events
        $upcomingEvents = Cache::remember('dashboard_upcoming_events', 300, function() {
            return Event::where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date')
                ->orderBy('time')
                ->take(5)
                ->get();
        });
            
        // Get recent attendances
        $recentAttendances = Cache::remember('dashboard_recent_attendances', 300, function() {
            return Attendance::with(['user', 'event'])
                ->latest()
                ->take(10)
                ->get();
        });
            
        // Get attendance statistics by status
        $attendanceByStatus = Cache::remember('dashboard_attendance_by_status', 300, function() {
            return Attendance::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
            
        // Get user statistics by role
        $usersByRole = User::select('roles.name', DB::raw('count(*) as count'))
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();
            
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEvents',
            'totalAttendances',
            'totalNotifications',
            'pendingRegistrations',
            'recentUsers',
            'upcomingEvents',
            'recentAttendances',
            'attendanceByStatus',
            'usersByRole'
        ));
    }
}
