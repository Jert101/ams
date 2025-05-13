<?php

namespace App\Http\Controllers\Secretary;

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
     * Display the secretary dashboard.
     */
    public function index()
    {
        // Get recent events
        $recentEvents = Event::orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();
            
        // Get upcoming events
        $upcomingEvents = Event::where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();
            
        // Get users with consecutive absences
        $usersWithConsecutiveAbsences = User::whereHas('attendances', function ($query) {
                $query->where('status', 'absent');
            })
            ->withCount(['attendances as consecutive_absences' => function ($query) {
                $query->where('status', 'absent')
                    ->whereIn('event_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('events')
                            ->orderBy('date', 'desc')
                            ->orderBy('time', 'desc')
                            ->limit(3);
                    });
            }])
            ->having('consecutive_absences', '>=', 2)
            ->orderBy('consecutive_absences', 'desc')
            ->take(10)
            ->get();
            
        // Get recent notifications
        $recentNotifications = Notification::with('user')
            ->latest()
            ->take(10)
            ->get();
            
        // Get attendance statistics
        $attendanceStats = Attendance::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        // Get notification statistics
        $notificationStats = [
            'total' => Notification::count(),
            'sent' => Notification::where('is_sent', true)->count(),
            'read' => Notification::whereNotNull('read_at')->count(),
        ];
        
        return view('secretary.dashboard', compact(
            'recentEvents',
            'upcomingEvents',
            'usersWithConsecutiveAbsences',
            'recentNotifications',
            'attendanceStats',
            'notificationStats'
        ));
    }
}
