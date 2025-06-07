<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            ->where('user_id', $user->user_id);
            
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
            'present' => Attendance::where('user_id', $user->user_id)->where('status', 'present')->count(),
            'absent' => Attendance::where('user_id', $user->user_id)->where('status', 'absent')->count(),
            'excused' => Attendance::where('user_id', $user->user_id)->where('status', 'excused')->count(),
            'pending' => Attendance::where('user_id', $user->user_id)->where('status', 'pending')->count(),
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
        if ($attendance->user_id !== $user->user_id) {
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
        $attendances = Attendance::where('user_id', $user->user_id)
            ->whereIn('event_id', $eventIds)
            ->get()
            ->keyBy('event_id');
            
        // Calculate attendance statistics
        $presentCount = 0;
        $absentCount = 0;
        $excusedCount = 0;
        $pendingCount = 0;
        
        foreach ($events as $event) {
            if (isset($attendances[$event->id])) {
                $status = $attendances[$event->id]->status;
                if ($status === 'present') {
                    $presentCount++;
                } elseif ($status === 'absent') {
                    $absentCount++;
                } elseif ($status === 'excused') {
                    $excusedCount++;
                } elseif ($status === 'pending') {
                    $pendingCount++;
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
            'pendingCount',
            'totalEvents', 
            'attendancePercentage'
        ));
    }

    /**
     * Show form for submitting attendance with selfie
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get today's events
        $todayEvents = Event::whereDate('date', now()->toDateString())
            ->where('is_active', true)
            ->orderBy('time')
            ->get();
            
        // Check if user already has attendance for these events
        $eventIds = $todayEvents->pluck('id')->toArray();
        $existingAttendances = Attendance::where('user_id', $user->user_id)
            ->whereIn('event_id', $eventIds)
            ->whereIn('status', ['present', 'pending'])
            ->get()
            ->keyBy('event_id');
            
        return view('member.attendances.create', compact('todayEvents', 'existingAttendances'));
    }
    
    /**
     * Store a newly created attendance record with selfie
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'selfie' => 'required|image|max:5120', // 5MB max
        ]);
        
        $event = Event::findOrFail($validated['event_id']);
        
        // Check if user already has attendance for this event
        $existingAttendance = Attendance::where('user_id', $user->user_id)
            ->where('event_id', $event->id)
            ->whereIn('status', ['present', 'pending'])
            ->first();
            
        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You have already submitted attendance for this event.');
        }
        
        // Store the selfie
        $selfiePath = $request->file('selfie')->store('selfies', 'public');
        
        // Create attendance record
        Attendance::create([
            'user_id' => $user->user_id,
            'event_id' => $event->id,
            'status' => 'pending',
            'selfie_path' => $selfiePath,
            'remarks' => $request->input('remarks'),
        ]);
        
        return redirect()->route('member.attendances.index')
            ->with('success', 'Attendance submitted successfully. Waiting for approval.');
    }
}
