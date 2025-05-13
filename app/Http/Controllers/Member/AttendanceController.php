<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the member's attendances.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        
        $query = Attendance::with('event')
            ->where('user_id', $user->id);
            
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
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $attendances = $query->latest()->paginate(10);
        
        // Get attendance statistics
        $stats = [
            'present' => Attendance::where('user_id', $user->id)->where('status', 'present')->count(),
            'absent' => Attendance::where('user_id', $user->id)->where('status', 'absent')->count(),
            'excused' => Attendance::where('user_id', $user->id)->where('status', 'excused')->count(),
        ];
        
        $totalAttendances = array_sum($stats);
        $attendancePercentage = $totalAttendances > 0 
            ? round(($stats['present'] / $totalAttendances) * 100, 2) 
            : 0;
            
        return view('member.attendances.index', compact(
            'attendances', 
            'stats', 
            'attendancePercentage', 
            'startDate', 
            'endDate', 
            'status'
        ));
    }
    
    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        $user = Auth::user();
        
        // Ensure the attendance record belongs to the authenticated user
        if ($attendance->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('member.attendances.show', compact('attendance'));
    }
    
    /**
     * Display the monthly attendance report for the member.
     */
    public function monthly(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        
        // Get all events for the selected month
        $events = Event::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->orderBy('time')
            ->get();
            
        // Get the user's attendances for these events
        $eventIds = $events->pluck('id')->toArray();
        $attendances = Attendance::where('user_id', $user->id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');
            
        // Calculate attendance statistics
        $presentCount = 0;
        $absentCount = 0;
        $excusedCount = 0;
        
        foreach ($events as $event) {
            if (isset($attendances[$event->id])) {
                $status = $attendances[$event->id]->status;
                if ($status === 'present') {
                    $presentCount++;
                } elseif ($status === 'absent') {
                    $absentCount++;
                } elseif ($status === 'excused') {
                    $excusedCount++;
                }
            } else {
                // No record means absent
                $absentCount++;
            }
        }
        
        $totalEvents = $events->count();
        $attendancePercentage = $totalEvents > 0 
            ? round(($presentCount / $totalEvents) * 100, 2) 
            : 0;
            
        return view('member.attendances.monthly', compact(
            'events', 
            'attendances', 
            'year', 
            'month', 
            'presentCount', 
            'absentCount', 
            'excusedCount', 
            'totalEvents', 
            'attendancePercentage'
        ));
    }
}
