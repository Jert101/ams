<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the officer dashboard.
     */
    public function index()
    {
        // Get today's date for filtering
        $today = now()->format('Y-m-d');
        
        // Get active events for today and upcoming
        $todayEvents = Event::where('date', $today)
            ->where('is_active', true)
            ->orderBy('time')
            ->get();
            
        $upcomingEvents = Event::where('date', '>', $today)
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();
            
        // Get recent attendances recorded by this officer
        $recentAttendances = Attendance::with(['user', 'event'])
            ->where('approved_by', auth()->id())
            ->latest()
            ->take(10)
            ->get();
            
        // Get attendance statistics for today's events
        $todayEventIds = $todayEvents->pluck('id')->toArray();
        $todayStats = [];
        
        if (!empty($todayEventIds)) {
            $todayStats = Attendance::whereIn('event_id', $todayEventIds)
                ->select('event_id', 'status', DB::raw('count(*) as count'))
                ->groupBy('event_id', 'status')
                ->get()
                ->groupBy('event_id')
                ->map(function ($items) {
                    return $items->pluck('count', 'status')->toArray();
                })
                ->toArray();
        }
        
        // Get total members for reference
        $totalMembers = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        })->count();
        
        return view('officer.dashboard', compact(
            'todayEvents',
            'upcomingEvents',
            'recentAttendances',
            'todayStats',
            'totalMembers'
        ));
    }
}
