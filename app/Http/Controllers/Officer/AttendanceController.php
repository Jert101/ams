<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
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
            'approved_by' => Auth::id(),
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
                Attendance::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'status' => 'present',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            } else {
                // Update existing record
                $existingAttendance->update([
                    'status' => 'present',
                    'approved_by' => Auth::id(),
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
                Attendance::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'status' => 'absent',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            } else {
                // Update existing record
                $existingAttendance->update([
                    'status' => 'absent',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            }
        }
        
        return redirect()->route('officer.attendances.index', ['event_id' => $eventId])
            ->with('success', count($userIds) . ' users marked as absent.');
    }
}
