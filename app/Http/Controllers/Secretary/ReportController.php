<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Notification;

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
            
        // Generate CSV content with proper UTF-8 BOM for Excel compatibility
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        
        // Add report title and date range
        $csv .= "Attendance Report: " . Carbon::parse($startDate)->format('M j, Y') . " to " . Carbon::parse($endDate)->format('M j, Y') . "\n\n";
        
        // Add headers
        $headers = ['Member ID', 'Member Name', 'Email'];
        
        // Add event headers with properly formatted dates
        foreach ($events as $event) {
            $headers[] = $event->name . ' (' . Carbon::parse($event->date)->format('M j, Y') . ')';
        }
        
        $csv .= implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";
        
        // Add member data
        foreach ($members as $member) {
            $row = [
                $member->id,
                $member->name,
                $member->email
            ];
            
            foreach ($events as $event) {
                if (isset($attendances[$member->id][$event->id])) {
                    $status = ucfirst($attendances[$member->id][$event->id][0]->status);
                } else {
                    $status = 'Not Recorded';
                }
                $row[] = $status;
            }
            
            $csv .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }
        
        // Add summary information
        $csv .= "\nSummary Statistics\n";
        $csv .= "Total Members," . $members->count() . "\n";
        $csv .= "Total Events," . $events->count() . "\n";
        
        // Calculate attendance statistics
        $totalAttendances = Attendance::whereIn('event_id', $eventIds)->count();
        $presentCount = Attendance::whereIn('event_id', $eventIds)->where('status', 'present')->count();
        $absentCount = Attendance::whereIn('event_id', $eventIds)->where('status', 'absent')->count();
        $excusedCount = Attendance::whereIn('event_id', $eventIds)->where('status', 'excused')->count();
        
        $presentPercentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;
        
        $csv .= "Present Count," . $presentCount . "\n";
        $csv .= "Absent Count," . $absentCount . "\n";
        $csv .= "Excused Count," . $excusedCount . "\n";
        $csv .= "Attendance Rate," . $presentPercentage . "%\n";
        
        $csv .= "\nGenerated on: " . Carbon::now()->format('M j, Y h:i A') . "\n";
        $csv .= "Knights of the Altar - Attendance Monitoring System\n";
        
        $filename = 'attendance_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Export list of members with 3 consecutive Sunday absences.
     */
    public function exportThreeConsecutiveAbsences()
    {
        $report = $this->generateConsecutiveAbsencesReport(3);
        
        $filename = 'members_with_3_consecutive_absences_' . Carbon::now()->format('Y-m-d') . '.csv';
        
        return response($report)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Export list of members with 4 or more consecutive Sunday absences.
     */
    public function exportFourPlusConsecutiveAbsences()
    {
        $report = $this->generateConsecutiveAbsencesReport(4);
        
        $filename = 'members_with_4plus_consecutive_absences_' . Carbon::now()->format('Y-m-d') . '.csv';
        
        return response($report)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Generate the report for members with consecutive Sunday absences.
     */
    private function generateConsecutiveAbsencesReport($minConsecutiveAbsences)
    {
        // Get only Sunday events/masses
        $sundayEvents = Event::where(function ($query) {
                $query->where('type', 'sunday')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                            ->orWhere('name', 'like', '%Sunday%');
                    });
            })
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');
            
        // Extract unique Sunday dates
        $recentSundayDates = $sundayEvents->keys()->take(10)->toArray();
            
        // Get all members
        $members = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            })->get();
            
        // Members with the required consecutive absences
        $membersWithConsecutiveAbsences = [];
        
        foreach ($members as $member) {
            $sundayAbsenceCount = 0;
            $missedSundays = [];
            
            foreach ($recentSundayDates as $date) {
                // Get all masses for this Sunday
                $sundayMassIds = $sundayEvents[$date]->pluck('id')->toArray();
                
                if (empty($sundayMassIds)) {
                    continue;
                }
                
                // Check if member attended any of the masses on this Sunday
                $attendedCount = Attendance::where('user_id', $member->id)
                    ->whereIn('event_id', $sundayMassIds)
                    ->where('status', 'present')
                    ->count();
                
                // If they didn't attend any masses on this Sunday, count as absent
                if ($attendedCount === 0) {
                    $sundayAbsenceCount++;
                    $missedSundays[] = $date;
                } else {
                    // Break the consecutive chain if they attended a mass
                    break;
                }
            }
            
            // Get the last notification sent to this member
            $lastNotification = Notification::where('user_id', $member->id)
                ->where('type', 'absence_warning')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $lastNotificationDate = $lastNotification ? $lastNotification->created_at->format('M j, Y') : 'Never';
            
            // Format missed Sundays nicely
            $missedSundaysStr = implode(', ', array_map(function($date) {
                return Carbon::parse($date)->format('M j, Y');
            }, $missedSundays));
            
            // Only include members with the required number of consecutive absences
            if ($minConsecutiveAbsences == 3 && $sundayAbsenceCount == 3) {
                $membersWithConsecutiveAbsences[] = [
                    'member' => $member,
                    'absences' => $sundayAbsenceCount,
                    'missed_sundays' => $missedSundays
                ];
            } else if ($minConsecutiveAbsences == 4 && $sundayAbsenceCount >= 4) {
                $membersWithConsecutiveAbsences[] = [
                    'member' => $member,
                    'absences' => $sundayAbsenceCount,
                    'missed_sundays' => $missedSundays
                ];
            }
        }
        
        // Generate CSV content with proper UTF-8 BOM for Excel compatibility
        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM
        
        // Add report title and date
        if ($minConsecutiveAbsences == 3) {
            $csv .= "MEMBERS WITH 3 CONSECUTIVE SUNDAY ABSENCES\n";
            $csv .= "Action Required: Counseling at next meeting\n\n";
        } else {
            $csv .= "MEMBERS WITH 4+ CONSECUTIVE SUNDAY ABSENCES\n";
            $csv .= "Action Required: Serious Counseling / Home Visit\n\n";
        }
        
        // If no members found
        if (empty($membersWithConsecutiveAbsences)) {
            $csv .= "No members found with " . ($minConsecutiveAbsences == 3 ? "exactly 3" : "4 or more") . " consecutive Sunday absences.\n\n";
            $csv .= "Report generated on: " . Carbon::now()->format('F j, Y h:i A') . "\n";
            $csv .= "Knights of the Altar - Attendance Monitoring System\n";
            return $csv;
        }
        
        // Add headers
        $headers = [
            'Member ID', 
            'Name', 
            'Email', 
            'Phone', 
            'Address', 
            'Consecutive Absences', 
            'Missed Sundays (Most Recent First)',
            'Last Notification Date'
        ];
        
        $csv .= implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";
        
        // Add member data
        foreach ($membersWithConsecutiveAbsences as $data) {
            $member = $data['member'];
            
            // Get the last notification sent to this member
            $lastNotification = Notification::where('user_id', $member->id)
                ->where('type', 'absence_warning')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $lastNotificationDate = $lastNotification ? $lastNotification->created_at->format('M j, Y') : 'Never';
            
            // Format missed Sundays nicely
            $missedSundaysStr = implode(', ', array_map(function($date) {
                return Carbon::parse($date)->format('M j, Y');
            }, $data['missed_sundays']));
            
            $row = [
                $member->id,
                $member->name,
                $member->email,
                $member->phone ?? 'N/A',
                $member->address ?? 'N/A',
                $data['absences'],
                $missedSundaysStr,
                $lastNotificationDate
            ];
            
            $csv .= implode(',', array_map(function($field) {
                // Escape fields that might contain commas
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }
        
        // Add summary information
        $csv .= "\nSummary Information\n";
        $csv .= "Total Members Listed," . count($membersWithConsecutiveAbsences) . "\n";
        
        if ($minConsecutiveAbsences == 3) {
            $csv .= "Required Action,Counseling at next meeting\n";
        } else {
            $csv .= "Required Action,Serious Counseling / Home Visit\n";
        }
        
        $csv .= "\nAttendance Rule: A member is marked as absent for a Sunday only if they miss ALL 4 masses on that day.\n";
        $csv .= "Attending at least one mass on Sunday counts as present for that Sunday.\n\n";
        
        $csv .= "Report generated on: " . Carbon::now()->format('F j, Y h:i A') . "\n";
        $csv .= "Knights of the Altar - Attendance Monitoring System\n";
        
        return $csv;
    }
    
    /**
     * Export list of members with 3 consecutive Sunday absences as PDF.
     */
    public function exportThreeConsecutiveAbsencesPdf()
    {
        $report = $this->generateConsecutiveAbsencesReport(3);
        $filename = 'members_with_3_consecutive_absences_' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        // Create a collection of members with consecutive absences
        $membersWithConsecutiveAbsences = $this->parseConsecutiveAbsencesForPdf(3);
        
        $pdf = \PDF::loadView('pdfs.consecutive-absences-report', [
            'title' => 'MEMBERS WITH 3 CONSECUTIVE SUNDAY ABSENCES',
            'subtitle' => 'Action Required: Counseling at next meeting',
            'members' => $membersWithConsecutiveAbsences,
            'today' => Carbon::now()->format('F j, Y h:i A')
        ]);
        
        return $pdf->download($filename);
    }
    
    /**
     * Export list of members with 4 or more consecutive Sunday absences as PDF.
     */
    public function exportFourPlusConsecutiveAbsencesPdf()
    {
        $report = $this->generateConsecutiveAbsencesReport(4);
        $filename = 'members_with_4plus_consecutive_absences_' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        // Create a collection of members with consecutive absences
        $membersWithConsecutiveAbsences = $this->parseConsecutiveAbsencesForPdf(4);
        
        $pdf = \PDF::loadView('pdfs.consecutive-absences-report', [
            'title' => 'MEMBERS WITH 4+ CONSECUTIVE SUNDAY ABSENCES',
            'subtitle' => 'Action Required: Serious Counseling / Home Visit',
            'members' => $membersWithConsecutiveAbsences,
            'today' => Carbon::now()->format('F j, Y h:i A')
        ]);
        
        return $pdf->download($filename);
    }
    
    /**
     * Parse consecutive absences data for PDF generation.
     */
    private function parseConsecutiveAbsencesForPdf($minConsecutiveAbsences)
    {
        // Get only Sunday events/masses
        $sundayEvents = Event::where(function ($query) {
                $query->where('type', 'sunday')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereRaw("DAYOFWEEK(date) = 1") // 1 represents Sunday in MySQL
                            ->orWhere('name', 'like', '%Sunday%');
                    });
            })
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');
            
        // Extract unique Sunday dates
        $recentSundayDates = $sundayEvents->keys()->take(10)->toArray();
            
        // Get all members
        $members = User::whereHas('role', function ($query) {
                $query->where('name', 'Member');
            })->get();
            
        // Members with the required consecutive absences
        $membersWithConsecutiveAbsences = collect();
        
        foreach ($members as $member) {
            $sundayAbsenceCount = 0;
            $missedSundays = [];
            
            foreach ($recentSundayDates as $date) {
                // Get all masses for this Sunday
                $sundayMassIds = $sundayEvents[$date]->pluck('id')->toArray();
                
                if (empty($sundayMassIds)) {
                    continue;
                }
                
                // Check if member attended any of the masses on this Sunday
                $attendedCount = Attendance::where('user_id', $member->id)
                    ->whereIn('event_id', $sundayMassIds)
                    ->where('status', 'present')
                    ->count();
                
                // If they didn't attend any masses on this Sunday, count as absent
                if ($attendedCount === 0) {
                    $sundayAbsenceCount++;
                    $missedSundays[] = $date;
                } else {
                    // Break the consecutive chain if they attended a mass
                    break;
                }
            }
            
            // Get the last notification sent to this member
            $lastNotification = Notification::where('user_id', $member->id)
                ->where('type', 'absence_warning')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $lastNotificationDate = $lastNotification ? $lastNotification->created_at->format('M j, Y') : 'Never';
            
            // Format missed Sundays nicely
            $missedSundaysStr = implode(', ', array_map(function($date) {
                return Carbon::parse($date)->format('M j, Y');
            }, $missedSundays));
            
            // Only include members with the required number of consecutive absences
            if ($minConsecutiveAbsences == 3 && $sundayAbsenceCount == 3) {
                $membersWithConsecutiveAbsences->push([
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone ?? 'N/A',
                    'address' => $member->address ?? 'N/A',
                    'absences' => $sundayAbsenceCount,
                    'missed_sundays' => $missedSundaysStr,
                    'last_notification' => $lastNotificationDate
                ]);
            } else if ($minConsecutiveAbsences == 4 && $sundayAbsenceCount >= 4) {
                $membersWithConsecutiveAbsences->push([
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone ?? 'N/A',
                    'address' => $member->address ?? 'N/A',
                    'absences' => $sundayAbsenceCount,
                    'missed_sundays' => $missedSundaysStr,
                    'last_notification' => $lastNotificationDate
                ]);
            }
        }
        
        return $membersWithConsecutiveAbsences;
    }
}
