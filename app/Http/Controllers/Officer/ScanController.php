<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\User;
use App\Models\MassSchedule;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    protected $mailService;
    
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    /**
     * Display the ID scanning interface.
     */
    public function index()
    {
        // Get active events for the current day only
        $today = Carbon::today()->format('Y-m-d');
        
        // Get mass schedules for today
        $todayMassSchedules = MassSchedule::with('event')
            ->whereHas('event', function($query) use ($today) {
                $query->where('date', $today)
                    ->where('is_active', true);
            })
            ->get();
            
        // Get other active events for today that are not masses
        $todayOtherEvents = Event::where('is_active', true)
            ->where('date', $today)
            ->whereDoesntHave('massSchedule')
            ->get();
            
        // Combine both collections
        $activeEvents = $todayMassSchedules->map(function($massSchedule) {
            return $massSchedule->event;
        })->concat($todayOtherEvents);
        
        return view('officer.scan.index', compact('activeEvents'));
    }
    
    /**
     * Process a scanned ID.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'event_id' => 'required',
        ]);
        
        // Use real data mode
        $testMode = false;
        
        // Find the user by ID
        $user = User::where('user_id', $validated['qr_code'])->first();
            
        // In test mode, create a dummy user if not found
        if (!$user && $testMode) {
            // Get a test user (first admin or create one)
            $user = User::where('role_id', 1)->first();
            
            if (!$user) {
                // If no admin user exists, use the authenticated user
                $user = Auth::user();
            }
            
            // Create or find an event for testing
            $event = Event::firstOrCreate(
                ['id' => $validated['event_id']],
                [
                    'name' => 'Test Event',
                    'description' => 'Automatically created test event',
                    'date' => now()->format('Y-m-d'),
                    'time' => now()->format('H:i:s'),
                    'location' => 'Test Location',
                    'is_active' => true,
                    'created_by' => Auth::user()->user_id
                ]
            );
            
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('user_id', $user->user_id)
                ->where('event_id', $event->id)
                ->first();
                
            if ($existingAttendance) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance already recorded for this user and event.',
                    'user' => $user->name,
                    'user_id' => $user->user_id,
                    'event' => $event->name,
                    'event_id' => $event->id,
                    'status' => $existingAttendance->status,
                    'approved_by' => $existingAttendance->approved_by
                ]);
            }
            
            // Create a new attendance record
            $attendance = Attendance::create([
                'user_id' => $user->user_id,
                'event_id' => $event->id,
                'status' => 'present',
                'approved_by' => Auth::user()->user_id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test attendance recorded successfully in database.',
                'user' => $user->name,
                'user_id' => $user->user_id,
                'event' => $event->name,
                'event_id' => $event->id,
                'status' => 'present',
                'approved_by' => Auth::user()->user_id
            ]);
        } else if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID. No user found.'
            ]);
        }
        
        // Get the event - in test mode, we'll create a dummy event if needed
        try {
            $event = Event::findOrFail($validated['event_id']);
        } catch (\Exception $e) {
            if ($testMode) {
                // Return a success response with dummy data for testing
                return response()->json([
                    'success' => true,
                    'message' => 'Test attendance recorded successfully.',
                    'user' => $user->name,
                    'user_id' => $user->user_id,
                    'event' => 'Test Event',
                    'event_id' => $validated['event_id'],
                    'status' => 'present (test)',
                    'approved_by' => Auth::user()->user_id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.'
                ]);
            }
        }
        
        // Check if this event has a mass schedule and if attendance is allowed
        $massSchedule = $event->massSchedule;
        if ($massSchedule && !$massSchedule->isAttendanceAllowed()) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance is not allowed at this time for this mass schedule.',
                'user' => $user->name,
                'user_id' => $user->user_id,
                'event' => $event->name,
                'event_id' => $event->id,
                'mass_type' => $massSchedule->type,
                'mass_order' => $massSchedule->mass_order,
                'attendance_window' => $massSchedule->formatted_attendance_start_time . ' - ' . $massSchedule->formatted_attendance_end_time
            ]);
        }
        
        // Check if attendance record already exists
        $existingAttendance = Attendance::where('user_id', $user->user_id)
            ->where('event_id', $event->id)
            ->first();
            
        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already recorded for this user and event.',
                'user' => $user->name,
                'user_id' => $user->user_id,
                'event' => $event->name,
                'event_id' => $event->id,
                'status' => $existingAttendance->status,
                'approved_by' => Auth::user()->user_id
            ]);
        }
        
        // Create new attendance record
        $attendance = Attendance::create([
            'user_id' => $user->user_id,
            'event_id' => $event->id,
            'status' => 'present',
            'approved_by' => Auth::user()->user_id,
            'approved_at' => now(),
        ]);
        
        // Send email notification to the user
        $this->sendAttendanceEmail($user, $event, $attendance);
        
        $responseData = [
            'success' => true,
            'message' => 'Attendance recorded successfully.',
            'user' => $user->name,
            'user_id' => $user->user_id,
            'event' => $event->name,
            'event_id' => $event->id,
            'status' => 'present',
            'approved_by' => Auth::user()->user_id
        ];
        
        // Add mass schedule info if available
        if ($massSchedule) {
            $responseData['mass_type'] = $massSchedule->type;
            $responseData['mass_order'] = $massSchedule->mass_order;
        }
        
        return response()->json($responseData);
    }
    
    /**
     * Create a quick mass schedule for today.
     */
    public function quickMassStore(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:special_mass,other',
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required|after:attendance_start_time',
            'location' => 'required|string|max:255',
        ]);
        
        // Create event first
        $event = Event::create([
            'name' => $validated['name'],
            'type' => $validated['type'] === 'other' ? 'other' : 'special_mass',
            'date' => Carbon::today()->format('Y-m-d'),
            'time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'attendance_start_time' => $validated['attendance_start_time'],
            'attendance_end_time' => $validated['attendance_end_time'],
            'location' => $validated['location'],
            'is_active' => true,
            'created_by' => Auth::user()->user_id
        ]);
        
        // If it's a mass, create a mass schedule
        if ($validated['type'] === 'special_mass') {
            $massSchedule = MassSchedule::create([
                'event_id' => $event->id,
                'type' => 'special_mass',
                'mass_order' => null,
                'attendance_start_time' => $validated['attendance_start_time'],
                'attendance_end_time' => $validated['attendance_end_time'],
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Mass created successfully',
            'event' => $event,
            'formatted_time' => Carbon::parse($event->time)->format('h:i A')
        ]);
    }
    
    /**
     * Send attendance confirmation email.
     */
    private function sendAttendanceEmail(User $user, Event $event, Attendance $attendance)
    {
        if (!$user->email) {
            return false;
        }
        
        $data = [
            'event_name' => $event->name,
            'event_date' => Carbon::parse($event->date)->format('F j, Y'),
            'event_time' => Carbon::parse($event->time)->format('g:i A'),
            'event_location' => $event->location,
            'attendance_status' => $attendance->status,
            'recorded_at' => $attendance->created_at->format('F j, Y g:i A')
        ];
        
        return $this->mailService->sendAttendanceConfirmation($user->email, $user->name, $data);
    }
}