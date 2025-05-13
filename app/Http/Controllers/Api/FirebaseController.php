<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseController extends Controller
{
    /**
     * Sync data between Laravel and Firebase.
     */
    public function sync(Request $request)
    {
        try {
            // Get data to sync
            $lastSync = $request->input('last_sync');
            
            // Query for data that needs to be synced
            $query = Event::query();
            if ($lastSync) {
                $query->where('updated_at', '>=', $lastSync);
            }
            $events = $query->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => $event->date->format('Y-m-d'),
                    'time' => $event->time ? $event->time->format('H:i:s') : null,
                    'description' => $event->description,
                    'is_active' => $event->is_active,
                    'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $event->updated_at->format('Y-m-d H:i:s'),
                ];
            });
            
            // Get attendances
            $attendanceQuery = Attendance::with(['user', 'event']);
            if ($lastSync) {
                $attendanceQuery->where('updated_at', '>=', $lastSync);
            }
            $attendances = $attendanceQuery->get()->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'user_name' => $attendance->user ? $attendance->user->name : null,
                    'event_id' => $attendance->event_id,
                    'event_name' => $attendance->event ? $attendance->event->name : null,
                    'status' => $attendance->status,
                    'approved_by' => $attendance->approved_by,
                    'approved_at' => $attendance->approved_at ? $attendance->approved_at->format('Y-m-d H:i:s') : null,
                    'remarks' => $attendance->remarks,
                    'created_at' => $attendance->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $attendance->updated_at->format('Y-m-d H:i:s'),
                ];
            });
            
            // Get users (only basic info for security)
            $userQuery = User::query();
            if ($lastSync) {
                $userQuery->where('updated_at', '>=', $lastSync);
            }
            $users = $userQuery->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first() ? $user->roles->first()->name : null,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Data synced successfully',
                'events' => $events,
                'attendances' => $attendances,
                'users' => $users,
                'sync_time' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase sync error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Record attendance via API.
     */
    public function recordAttendance(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'event_id' => 'required|exists:events,id',
                'status' => 'required|in:present,absent,excused',
                'approved_by' => 'nullable|exists:users,id',
                'remarks' => 'nullable|string|max:255',
            ]);
            
            // Check if attendance record already exists
            $existingAttendance = Attendance::where('user_id', $validated['user_id'])
                ->where('event_id', $validated['event_id'])
                ->first();
                
            if ($existingAttendance) {
                // Update existing record
                $existingAttendance->update([
                    'status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                    'approved_by' => $validated['approved_by'] ?? auth()->id(),
                    'approved_at' => now(),
                ]);
                
                $attendance = $existingAttendance;
            } else {
                // Create new attendance record
                $attendance = Attendance::create([
                    'user_id' => $validated['user_id'],
                    'event_id' => $validated['event_id'],
                    'status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                    'approved_by' => $validated['approved_by'] ?? auth()->id(),
                    'approved_at' => now(),
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully',
                'attendance' => [
                    'id' => $attendance->id,
                    'user_id' => $attendance->user_id,
                    'user_name' => $attendance->user ? $attendance->user->name : null,
                    'event_id' => $attendance->event_id,
                    'event_name' => $attendance->event ? $attendance->event->name : null,
                    'status' => $attendance->status,
                    'approved_by' => $attendance->approved_by,
                    'approved_at' => $attendance->approved_at ? $attendance->approved_at->format('Y-m-d H:i:s') : null,
                    'remarks' => $attendance->remarks,
                    'created_at' => $attendance->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $attendance->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase attendance recording error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }
}