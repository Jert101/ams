<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming events
        $upcomingEvents = Event::where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();
            
        // Get the user's recent attendance records
        $recentAttendances = Attendance::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
            
        // Get the user's attendance statistics
        $attendanceStats = [
            'present' => Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'absent' => Attendance::where('user_id', $user->id)->where('status', 'absent')->count(),
            'excused' => Attendance::where('user_id', $user->id)->where('status', 'excused')->count(),
        ];
        
        $totalAttendances = array_sum($attendanceStats);
        $attendancePercentage = $totalAttendances > 0 
            ? round(($attendanceStats['present'] / $totalAttendances) * 100, 2) 
            : 0;
            
        // Get unread notifications for the user
        $notifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();
            
        // Get the user's QR code
        $qrCode = $user->qrCode;
        
        return view('member.dashboard', compact(
            'user',
            'upcomingEvents',
            'recentAttendances',
            'attendanceStats',
            'attendancePercentage',
            'notifications',
            'qrCode'
        ));
    }
}
