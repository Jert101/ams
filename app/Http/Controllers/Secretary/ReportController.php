<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get attendance statistics
        $totalAttendances = Attendance::count();
        $presentCount = Attendance::where('status', 'present')->count();
        $absentCount = Attendance::where('status', 'absent')->count();
        $excusedCount = Attendance::where('status', 'excused')->count();
        
        // Get user count per role
        $usersByRole = User::select('roles.name', DB::raw('count(*) as count'))
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();
            
        // Get recent events
        $recentEvents = Event::orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();
            
        // Get members for the member report form
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })
        ->orderBy('name')
        ->get();
        
        return view('secretary.reports.index', compact(
            'totalAttendances', 
            'presentCount', 
            'absentCount', 
            'excusedCount',
            'usersByRole',
            'recentEvents',
            'members'
        ));
    }
    
    /**
     * Generate attendance report by date range.
     */
    public function byDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        
        // Get events in the date range
        $events = Event::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
            
        // Get overall attendance statistics for these events
        $eventIds = $events->pluck('id')->toArray();
        
        $attendanceStats = Attendance::whereIn('event_id', $eventIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
            
        // Get attendance rates for each event
        $eventAttendanceRates = [];
        foreach ($events as $event) {
            $totalAttendances = Attendance::where('event_id', $event->id)->count();
            $presentCount = Attendance::where('event_id', $event->id)
                ->where('status', 'present')
                ->count();
                
            $eventAttendanceRates[$event->id] = [
                'name' => $event->name,
                'date' => $event->date,
                'total' => $totalAttendances,
                'present' => $presentCount,
                'rate' => $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0,
            ];
        }
        
        return view('secretary.reports.by-date-range', compact(
            'events', 
            'attendanceStats', 
            'eventAttendanceRates',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Generate attendance report by member.
     */
    public function byMember(Request $request)
    {
        $userId = $request->input('user_id');
        $startDate = $request->input('start_date', Carbon::now()->subMonths(1)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })
        ->orderBy('name')
        ->get();
        
        // Initialize member attendance data
        $memberData = null;
        
        if ($userId) {
            $member = User::findOrFail($userId);
            
            // Get events in the date range
            $events = Event::whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->orderBy('time')
                ->get();
                
            $eventIds = $events->pluck('id')->toArray();
            
            // Get the member's attendance for these events
            $attendances = Attendance::where('user_id', $userId)
                ->whereIn('event_id', $eventIds)
                ->get()
                ->keyBy('event_id');
                
            // Calculate statistics
            $totalEvents = count($eventIds);
            $attendedEvents = $attendances->where('status', 'present')->count();
            $absentEvents = $attendances->where('status', 'absent')->count();
            $excusedEvents = $attendances->where('status', 'excused')->count();
            $missedEvents = $totalEvents - $attendedEvents - $absentEvents - $excusedEvents;
            
            // Calculate attendance rate
            $attendanceRate = $totalEvents > 0 ? round(($attendedEvents / $totalEvents) * 100, 1) : 0;
            
            // Prepare event attendance details
            $eventAttendance = [];
            foreach ($events as $event) {
                $status = 'Not Recorded';
                
                if (isset($attendances[$event->id])) {
                    $status = ucfirst($attendances[$event->id]->status);
                }
                
                $eventAttendance[] = [
                    'name' => $event->name,
                    'date' => $event->date,
                    'status' => $status,
                ];
            }
            
            $memberData = [
                'member' => $member,
                'stats' => [
                    'total_events' => $totalEvents,
                    'attended_events' => $attendedEvents,
                    'absent_events' => $absentEvents,
                    'excused_events' => $excusedEvents,
                    'missed_events' => $missedEvents,
                    'attendance_rate' => $attendanceRate,
                ],
                'events' => $eventAttendance,
            ];
        }
        
        return view('secretary.reports.by-member', compact(
            'members',
            'memberData',
            'userId',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export attendance data to CSV.
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonths(1)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        // Get events in the date range
        $events = Event::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
            
        $eventIds = $events->pluck('id')->toArray();
        
        // Get all members
        $members = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })
        ->orderBy('name')
        ->get();
        
        // Get all attendances for these events
        $attendances = Attendance::whereIn('event_id', $eventIds)
            ->get()
            ->groupBy(['user_id', 'event_id']);
            
        // Generate CSV content
        $headers = ['Member ID', 'Member Name', 'Email'];
        
        // Add event headers
        foreach ($events as $event) {
            $headers[] = $event->name . ' (' . $event->date . ')';
        }
        
        $csv = implode(',', $headers) . "\n";
        
        foreach ($members as $member) {
            $row = [$member->id, $member->name, $member->email];
            
            foreach ($events as $event) {
                if (isset($attendances[$member->id][$event->id])) {
                    $status = ucfirst($attendances[$member->id][$event->id][0]->status);
                } else {
                    $status = 'Not Recorded';
                }
                $row[] = $status;
            }
            
            $csv .= implode(',', $row) . "\n";
        }
        
        $filename = 'attendance_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
