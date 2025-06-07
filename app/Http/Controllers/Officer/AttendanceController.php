<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    protected $mailService;
    
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    
    /**
     * Display a listing of attendances for officers to manage.
     */
    public function index(Request $request)
    {
        $eventId = $request->input('event_id');
        $status = $request->input('status');
        
        // Get all events for the filter dropdown
        $events = Event::orderBy('date', 'desc')->get();
        
        // Build the attendance query
        $query = Attendance::with(['user', 'event']);
        
        // Apply filters if provided
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get the attendances with pagination
        $attendances = $query->latest()->paginate(15);
        
        return view('officer.attendances.index', compact('attendances', 'events', 'eventId', 'status'));
    }
    
    /**
     * Show the form for editing an attendance record.
     */
    public function edit(Attendance $attendance)
    {
        return view('officer.attendances.edit', compact('attendance'));
    }
    
    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:present,absent,excused',
            'remarks' => 'nullable|string|max:255',
        ]);
        
        $attendance->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
            'approved_by' => Auth::user()->user_id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('officer.attendances.index')
            ->with('success', 'Attendance record updated successfully.');
    }
    
    /**
     * Mark multiple users as present for an event.
     */
    public function markPresent(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        $eventId = $validated['event_id'];
        $userIds = $validated['user_ids'];
        
        foreach ($userIds as $userId) {
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('user_id', $userId)
                ->where('event_id', $eventId)
                ->first();
                
            if (!$existingAttendance) {
                // Create new attendance record
                $attendance = Attendance::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'status' => 'present',
                    'approved_by' => Auth::user()->user_id,
                    'approved_at' => now(),
                ]);
            } else {
                // Update existing record
                $existingAttendance->update([
                    'status' => 'present',
                    'approved_by' => Auth::user()->user_id,
                    'approved_at' => now(),
                ]);
            }
        }
        
        return redirect()->route('officer.attendances.index', ['event_id' => $eventId])
            ->with('success', count($userIds) . ' users marked as present.');
    }
    
    /**
     * Mark multiple users as absent for an event.
     */
    public function markAbsent(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        $eventId = $validated['event_id'];
        $userIds = $validated['user_ids'];
        
        foreach ($userIds as $userId) {
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('user_id', $userId)
                ->where('event_id', $eventId)
                ->first();
                
            if (!$existingAttendance) {
                // Create new attendance record
                $attendance = Attendance::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'status' => 'absent',
                    'approved_by' => Auth::user()->user_id,
                    'approved_at' => now(),
                ]);
            } else {
                // Update existing record
                $existingAttendance->update([
                    'status' => 'absent',
                    'approved_by' => Auth::user()->user_id,
                    'approved_at' => now(),
                ]);
            }
        }
        
        return redirect()->route('officer.attendances.index', ['event_id' => $eventId])
            ->with('success', count($userIds) . ' users marked as absent.');
    }
    
    /**
     * Display a listing of pending attendances for verification.
     */
    public function pending()
    {
        $pendingAttendances = Attendance::with(['user', 'event'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
            
        return view('officer.attendances.pending', compact('pendingAttendances'));
    }
    
    /**
     * Show the form for verifying a pending attendance.
     */
    public function verify(Attendance $attendance)
    {
        if ($attendance->status !== 'pending') {
            return redirect()->route('officer.attendances.pending')
                ->with('error', 'This attendance record is not pending verification.');
        }
        
        return view('officer.attendances.verify', compact('attendance'));
    }
    
    /**
     * Process the verification of a pending attendance.
     */
    public function processVerification(Request $request, Attendance $attendance)
    {
        if ($attendance->status !== 'pending') {
            return redirect()->route('officer.attendances.pending')
                ->with('error', 'This attendance record is not pending verification.');
        }
        
        $validated = $request->validate([
            'verification' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:255',
        ]);
        
        $status = $validated['verification'] === 'approve' ? 'present' : 'absent';
        
        $attendance->update([
            'status' => $status,
            'remarks' => $validated['remarks'] ?? null,
            'approved_by' => Auth::user()->user_id,
            'approved_at' => now(),
        ]);
        
        // Send email notification to the member
        $user = $attendance->user;
        $event = $attendance->event;
        
        $data = [
            'event_name' => $event->name,
            'event_date' => $event->date->format('F j, Y'),
            'event_time' => $event->time->format('g:i A'),
            'event_location' => $event->location ?? 'N/A',
            'attendance_status' => $status,
            'recorded_at' => now()->format('F j, Y g:i A'),
            'verification_status' => $validated['verification'],
            'remarks' => $validated['remarks'] ?? 'No remarks provided.'
        ];
        
        $this->mailService->sendAttendanceConfirmation($user->email, $user->name, $data);
        
        return redirect()->route('officer.attendances.pending')
            ->with('success', 'Attendance verification processed successfully.');
    }
}
