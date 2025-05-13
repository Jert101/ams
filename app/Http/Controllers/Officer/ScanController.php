<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    /**
     * Display the QR code scanner interface.
     */
    public function index()
    {
        // Get active events for the dropdown
        $activeEvents = Event::where('is_active', true)
            ->whereDate('date', '>=', now()->subDays(1)->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time')
            ->get();
            
        return view('officer.scan.index', compact('activeEvents'));
    }
    
    /**
     * Process a scanned QR code.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);
        
        // Find the QR code
        $qrCode = QrCode::where('code', $validated['qr_code'])
            ->where('is_active', true)
            ->first();
            
        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive QR code.'
            ]);
        }
        
        // Get the user associated with the QR code
        $user = $qrCode->user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user associated with this QR code.'
            ]);
        }
        
        // Get the event
        $event = Event::findOrFail($validated['event_id']);
        
        // Check if attendance record already exists
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();
            
        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this user and event.',
                'user' => $user->name,
                'user_id' => $user->id,
                'event' => $event->name,
                'event_id' => $event->id,
                'status' => $existingAttendance->status,
                'approved_by' => Auth::id()
            ]);
        }
        
        // Create new attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'present',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        // Update QR code last used timestamp
        $qrCode->update(['last_used_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully.',
            'user' => $user->name,
            'user_id' => $user->id,
            'event' => $event->name,
            'event_id' => $event->id,
            'status' => 'present',
            'approved_by' => Auth::id()
        ]);
    }
}