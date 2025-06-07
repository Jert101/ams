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
        
        // First, get all Sunday events
        $sundayEventsQuery = Event::where(function ($q) {
            $q->where('type', 'sunday')
              ->orWhere(function ($subQuery) {
                  $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                          ->orWhere('name', 'like', '%Sunday%');
              });
        });
        
        if ($startDate) {
            $sundayEventsQuery->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $sundayEventsQuery->where('date', '<=', $endDate);
        }
        
        // Get Sunday events, grouped by date
        $sundayEvents = $sundayEventsQuery->orderBy('date', 'desc')->get();
        $sundaysByDate = $sundayEvents->groupBy('date');
        
        // For each Sunday, find users who missed ALL masses on that day
        $userSundayAbsences = [];
        $userIds = [];
        
        foreach ($sundaysByDate as $date => $events) {
            $eventIds = $events->pluck('id')->toArray();
            
            // Get all users who should have attended these events
            $members = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            });
            
            if ($userId) {
                $members->where('id', $userId);
            }
            
            $memberIds = $members->pluck('id')->toArray();
            
            foreach ($memberIds as $memberId) {
                // Count how many of the day's events this user attended
                $attendedCount = Attendance::where('user_id', $memberId)
                    ->whereIn('event_id', $eventIds)
                    ->where('status', 'present')
                    ->count();
                
                // If they attended none of the masses on this Sunday, mark them absent for the day
                if ($attendedCount === 0) {
                    // Check if they were marked absent for at least one of the events
                    $absentCount = Attendance::where('user_id', $memberId)
                        ->whereIn('event_id', $eventIds)
                        ->where('status', 'absent')
                        ->count();
                    
                    if ($absentCount > 0) {
                        // Use the first absence record for this Sunday
                        $absenceRecord = Attendance::with(['user', 'event'])
                            ->where('user_id', $memberId)
                            ->whereIn('event_id', $eventIds)
                            ->where('status', 'absent')
                            ->first();
                        
                        if ($absenceRecord) {
                            $userSundayAbsences[] = $absenceRecord;
                            $userIds[] = $memberId;
                        }
                    }
                }
            }
        }
        
        // Convert to paginated collection
        $absencesCollection = collect($userSundayAbsences);
        $page = request()->get('page', 1);
        $perPage = 15;
        $items = $absencesCollection->forPage($page, $perPage);
        $absences = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, 
            count($userSundayAbsences), 
            $perPage, 
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
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

        // Get the recent Sundays (dates)
        $recentSundayDates = Event::where(function ($query) {
                $query->where('type', 'sunday')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                            ->orWhere('name', 'like', '%Sunday%');
                    });
            })
            ->orderBy('date', 'desc')
            ->distinct()
            ->pluck('date')
            ->take(5)
            ->toArray();
            
        // Build basic query to find members
        $members = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            })->get();
            
        // Check each member for consecutive Sunday absences
        $usersWithConsecutiveAbsences = [];
        $userAbsences = [];
        
        foreach ($members as $member) {
            $sundayAbsenceCount = 0;
            $memberAbsences = [];
            
            foreach ($recentSundayDates as $date) {
                // Get all events for this Sunday
                $sundayEvents = Event::where('date', $date)->pluck('id')->toArray();
                
                if (empty($sundayEvents)) {
                    continue;
                }
                
                // Check if member attended any of the masses on this Sunday
                $attendedCount = Attendance::where('user_id', $member->id)
                    ->whereIn('event_id', $sundayEvents)
                    ->where('status', 'present')
                    ->count();
                
                // If they didn't attend any masses on this Sunday, count as absent
                if ($attendedCount === 0) {
                    $sundayAbsenceCount++;
                    
                    // Get one of their absence records for this Sunday
                    $absenceRecord = Attendance::with('event')
                        ->where('user_id', $member->id)
                        ->whereIn('event_id', $sundayEvents)
                        ->where('status', 'absent')
                        ->first();
                    
                    if ($absenceRecord) {
                        $memberAbsences[] = $absenceRecord;
                    }
                }
            }
            
            // Apply the filter
            if (($filter === '3' && $sundayAbsenceCount === 3) ||
                ($filter === '4' && $sundayAbsenceCount >= 4) ||
                (empty($filter) && $sundayAbsenceCount >= 2)) {
                
                $member->sunday_absences = $sundayAbsenceCount;
                $usersWithConsecutiveAbsences[] = $member;
                $userAbsences[$member->id] = $memberAbsences;
            }
        }
        
        // Sort by absence count
        usort($usersWithConsecutiveAbsences, function($a, $b) {
            return $b->sunday_absences - $a->sunday_absences;
        });
        
        $users = collect($usersWithConsecutiveAbsences);
        
        return view('secretary.absences.consecutive', compact('users', 'userAbsences', 'recentSundayDates'));
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
                
            // Get the recent Sundays (dates)
            $recentSundayDates = Event::where(function ($query) {
                    $query->where('type', 'sunday')
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                                ->orWhere('name', 'like', '%Sunday%');
                        });
                })
                ->orderBy('date', 'desc')
                ->distinct()
                ->pluck('date')
                ->take(3)
                ->toArray();
                
            $consecutiveSundayAbsences = 0;
            
            foreach ($recentSundayDates as $date) {
                // Get all events for this Sunday
                $sundayEvents = Event::where('date', $date)->pluck('id')->toArray();
                
                if (empty($sundayEvents)) {
                    continue;
                }
                
                // Check if member attended any of the masses on this Sunday
                $attendedCount = Attendance::where('user_id', $member->id)
                    ->whereIn('event_id', $sundayEvents)
                    ->where('status', 'present')
                    ->count();
                
                // If they didn't attend any masses on this Sunday, count as absent
                if ($attendedCount === 0) {
                    $consecutiveSundayAbsences++;
                }
            }
            
            $absenceCounts[$member->id] = [
                'total' => $totalAbsences,
                'consecutive' => $consecutiveSundayAbsences,
            ];
        }
        
        return view('secretary.absences.by-member', compact('members', 'absenceCounts'));
    }
}
