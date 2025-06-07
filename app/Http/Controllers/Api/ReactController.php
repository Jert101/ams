<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class ReactController extends Controller
{
    /**
     * Get user list data for admin
     */
    public function getUserList()
    {
        $users = User::with('role')->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
    
    /**
     * Get admin dashboard data
     */
    public function getAdminDashboardData()
    {
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalAttendances = Attendance::count();
        $totalNotifications = \App\Models\Notification::count();
        
        // Get recent users for display
        $recentUsers = User::with('role')->latest()->take(5)->get();
        
        // Get recent events for display
        $recentEvents = Event::latest()->take(5)->get();
        
        // Get upcoming events for display
        $upcomingEvents = Event::where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(5)
            ->get();
        
        // Get recent attendances for display
        $recentAttendances = Attendance::with(['user', 'event'])
            ->latest()
            ->take(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalEvents' => $totalEvents,
                'totalAttendances' => $totalAttendances,
                'totalNotifications' => $totalNotifications
            ],
            'recentUsers' => $recentUsers,
            'recentEvents' => $recentEvents,
            'upcomingEvents' => $upcomingEvents,
            'recentAttendances' => $recentAttendances
        ]);
    }
    
    /**
     * Get member dashboard data
     */
    public function getMemberDashboardData()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        // Get attendance statistics
        $presentCount = Attendance::where('user_id', $user->user_id)
            ->where('status', 'present')
            ->count();
            
        $absentCount = Attendance::where('user_id', $user->user_id)
            ->where('status', 'absent')
            ->count();
            
        $excusedCount = Attendance::where('user_id', $user->user_id)
            ->where('status', 'excused')
            ->count();
        
        // Calculate attendance rate
        $totalAttendances = $presentCount + $absentCount + $excusedCount;
        $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;
        
        // Get the next upcoming event
        $nextEvent = Event::where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->first();
        
        // Get the member's QR code
        $qrCode = $user->qrCode;
        
        // Get recent attendance history
        $recentAttendances = Attendance::with('event')
            ->where('user_id', $user->user_id)
            ->latest()
            ->take(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => [
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'excusedCount' => $excusedCount,
                'attendanceRate' => $attendanceRate,
                'nextEvent' => $nextEvent,
                'qrCode' => $qrCode,
                'recentAttendances' => $recentAttendances
            ]
        ]);
    }
    
    /**
     * Process QR code scan for attendance
     */
    public function processQRScan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'qr_code' => 'required|string',
            'event_id' => 'nullable|exists:events,id',
        ]);
        
        $userId = $request->user_id;
        $qrCode = $request->qr_code;
        $eventId = $request->event_id;
        
        // Get the user
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Verify QR code
        if ($user->qrCode && $user->qrCode->code !== $qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ], 400);
        }
        
        // Get event if not provided
        if (!$eventId) {
            $event = Event::whereDate('date', now()->toDateString())->first();
            
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'No event found for today'
                ], 404);
            }
            
            $eventId = $event->id;
        }
        
        // Check if attendance record already exists
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('event_id', $eventId)
            ->first();
            
        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded',
                'attendance' => $existingAttendance,
                'user' => $user
            ], 400);
        }
        
        // Create attendance record
        $attendance = new Attendance();
        $attendance->user_id = $user->user_id;
        $attendance->event_id = $eventId;
        $attendance->status = 'present';
        $attendance->approved_at = now();
        $attendance->recorded_by = Auth::user()->user_id;
        $attendance->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'attendance' => $attendance,
            'user' => $user
        ]);
    }
} 