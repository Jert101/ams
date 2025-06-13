<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get counts for dashboard stats
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalAttendances = Attendance::count();
        $totalNotifications = Notification::count();
        $pendingRegistrations = User::where('approval_status', 'pending')->count();
        
        // Get recent users with eager loading to avoid N+1 queries
        $recentUsers = User::with(['role', 'qrCode'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($user) {
                // Add profile_photo_url to the array
                $userData = $user->toArray();
                $userData['profile_photo_url'] = $user->profile_photo_url;
                return $userData;
            });
            
        // Get upcoming events
        $upcomingEvents = Event::where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();
            
        // Get recent attendances
        $recentAttendances = Attendance::with(['user', 'event'])
            ->latest()
            ->take(10)
            ->get();
            
        // Get attendance statistics by status
        $attendanceByStatus = Attendance::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
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
