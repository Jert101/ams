<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $eventId = $request->input('event_id');
        
        $query = Attendance::with(['user', 'event'])
            ->select('attendances.*');
            
        if ($startDate) {
            $query->whereHas('event', function ($q) use ($startDate) {
                $q->whereDate('date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->whereHas('event', function ($q) use ($endDate) {
                $q->whereDate('date', '<=', $endDate);
            });
        }
        
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        
        $attendances = $query->latest()->paginate(15);
        $events = Event::orderBy('date', 'desc')->get();
        
        // Get attendance statistics
        $stats = [
            'total_attendances' => Attendance::count(),
            'total_events' => Event::count(),
            'total_users' => User::count(),
            'attendance_by_status' => Attendance::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];
        
        return view('admin.reports.index', compact('attendances', 'events', 'stats', 'startDate', 'endDate', 'eventId'));
    }
    
    /**
     * Generate a detailed attendance report.
     */
    public function attendanceReport(Request $request)
    {
        $eventId = $request->input('event_id');
        $event = Event::findOrFail($eventId);
        
        $attendances = Attendance::with('user')
            ->where('event_id', $eventId)
            ->get();
            
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        
        $data = [
            'event' => $event,
            'attendances' => $attendances,
            'stats' => [
                'present' => $presentCount,
                'absent' => $absentCount,
                'excused' => $excusedCount,
                'total' => $attendances->count(),
                'present_percentage' => $attendances->count() > 0 ? round(($presentCount / $attendances->count()) * 100, 2) : 0,
            ],
        ];
        
        return view('admin.reports.attendance', $data);
    }
    
    /**
     * Generate a user attendance report.
     */
    public function userReport(Request $request)
    {
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $user = User::findOrFail($userId);
        
        $query = Attendance::with('event')
            ->where('user_id', $userId);
            
        if ($startDate) {
            $query->whereHas('event', function ($q) use ($startDate) {
                $q->whereDate('date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->whereHas('event', function ($q) use ($endDate) {
                $q->whereDate('date', '<=', $endDate);
            });
        }
        
        $attendances = $query->latest()->get();
        
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        
        $data = [
            'user' => $user,
            'attendances' => $attendances,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => [
                'present' => $presentCount,
                'absent' => $absentCount,
                'excused' => $excusedCount,
                'total' => $attendances->count(),
                'present_percentage' => $attendances->count() > 0 ? round(($presentCount / $attendances->count()) * 100, 2) : 0,
            ],
        ];
        
        return view('admin.reports.user', $data);
    }
}
