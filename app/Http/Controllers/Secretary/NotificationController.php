<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index(Request $request)
    {
        $query = Notification::with('user');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            if ($request->input('status') === 'sent') {
                $query->where('is_sent', true);
            } elseif ($request->input('status') === 'not_sent') {
                $query->where('is_sent', false);
            }
        }
        $notifications = $query->latest()->paginate(15);
        if ($request->ajax()) {
            return response()->json([
                'html' => view('secretary.notifications.partials.notification-list', ['notifications' => $notifications])->render()
            ]);
        }
        return view('secretary.notifications.index', compact('notifications'));
    }
    
    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('secretary.notifications.create', compact('users'));
    }
    
    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:50',
            'message' => 'required|string',
        ]);
        
        Notification::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'message' => $validated['message'],
            'is_sent' => false,
        ]);
        
        return redirect()->route('secretary.notifications.index')
            ->with('success', 'Notification created successfully.');
    }
    
    /**
     * Send notifications to users with consecutive absences.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'message' => 'required|string',
        ]);
        
        $userIds = $validated['user_ids'];
        $message = $validated['message'];
        $notificationCount = 0;
        
        foreach ($userIds as $userId) {
            // Get consecutive absences count
            $recentEvents = Event::orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->take(3)
                ->pluck('id')
                ->toArray();
                
            $consecutiveAbsences = 0;
            if (!empty($recentEvents)) {
                $consecutiveAbsences = Attendance::where('user_id', $userId)
                    ->where('status', 'absent')
                    ->whereIn('event_id', $recentEvents)
                    ->count();
            }
            
            // Create notification
            Notification::create([
                'user_id' => $userId,
                'type' => 'absence_warning',
                'message' => $message,
                'is_sent' => true,
                'sent_at' => now(),
                'consecutive_absences' => $consecutiveAbsences,
            ]);
            
            $notificationCount++;
        }
        
        return redirect()->back()
            ->with('success', $notificationCount . ' notifications sent successfully.');
    }
    
    /**
     * Send a notification to all members.
     */
    public function sendToAll(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'message' => 'required|string',
        ]);
        
        $type = $validated['type'];
        $message = $validated['message'];
        
        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })->get();
        
        $notificationCount = 0;
        foreach ($members as $member) {
            Notification::create([
                'user_id' => $member->id,
                'type' => $type,
                'message' => $message,
                'is_sent' => true,
                'sent_at' => now(),
            ]);
            
            $notificationCount++;
        }
        
        return redirect()->route('secretary.notifications.index')
            ->with('success', $notificationCount . ' notifications sent to all members.');
    }
    
    /**
     * Mark a notification as sent.
     */
    public function markAsSent(Notification $notification)
    {
        $notification->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Notification marked as sent.');
    }
    
    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        
        return redirect()->route('secretary.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
