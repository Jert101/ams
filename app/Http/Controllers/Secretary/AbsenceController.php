<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    /**
     * Display a listing of all absences.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        
        $query = Attendance::with(['user', 'event'])
            ->where('status', 'absent');
            
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
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $absences = $query->latest()->paginate(15);
        $users = User::orderBy('name')->get();
        
        return view('secretary.absences.index', compact('absences', 'users', 'startDate', 'endDate', 'userId'));
    }
    
    /**
     * Display a listing of users with consecutive absences.
     */
    public function consecutive(Request $request)
    {
        // Get the filter value
        $filter = $request->input('filter');

        // Get the most recent events
        $recentEvents = Event::orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();
            
        $recentEventIds = $recentEvents->pluck('id')->toArray();
        
        // Build basic query
        $query = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            })
            ->withCount(['attendances as recent_absences' => function ($query) use ($recentEventIds) {
                $query->where('status', 'absent')
                    ->whereIn('event_id', $recentEventIds);
            }]);
            
        // Apply filter if specified
        if ($filter === '3') {
            $query->having('recent_absences', '=', 3);
        } elseif ($filter === '4') {
            $query->having('recent_absences', '>=', 4);
        } else {
            $query->having('recent_absences', '>=', 2);
        }
        
        // Get the filtered users
        $users = $query->orderBy('recent_absences', 'desc')->get();
            
        // Get detailed absence information for each user
        $userAbsences = [];
        foreach ($users as $user) {
            $absences = Attendance::with('event')
                ->where('user_id', $user->id)
                ->where('status', 'absent')
                ->whereIn('event_id', $recentEventIds)
                ->orderBy('created_at', 'desc')
                ->get();
                
            $userAbsences[$user->id] = $absences;
        }
        
        return view('secretary.absences.consecutive', compact('users', 'userAbsences', 'recentEvents'));
    }
    
    /**
     * Update the status of an absence.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'status' => 'required|in:absent,excused',
            'remarks' => 'nullable|string|max:255',
        ]);
        
        $attendance->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Absence status updated successfully.');
    }
    
    /**
     * Generate a report of absences by member.
     */
    public function byMember()
    {
        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })->get();
        
        // Get absence counts for each member
        $absenceCounts = [];
        foreach ($members as $member) {
            $totalAbsences = Attendance::where('user_id', $member->id)
                ->where('status', 'absent')
                ->count();
                
            $consecutiveAbsences = 0;
            
            // Get the most recent events
            $recentEvents = Event::orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->take(3)
                ->pluck('id')
                ->toArray();
                
            if (!empty($recentEvents)) {
                $consecutiveAbsences = Attendance::where('user_id', $member->id)
                    ->where('status', 'absent')
                    ->whereIn('event_id', $recentEvents)
                    ->count();
            }
            
            $absenceCounts[$member->id] = [
                'total' => $totalAbsences,
                'consecutive' => $consecutiveAbsences,
            ];
        }
        
        return view('secretary.absences.by-member', compact('members', 'absenceCounts'));
    }
}
