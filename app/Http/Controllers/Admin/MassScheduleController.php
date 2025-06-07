<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MassSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MassScheduleController extends Controller
{
    /**
     * Display a listing of the mass schedules.
     */
    public function index()
    {
        $sundaySchedules = MassSchedule::where('type', 'sunday_mass')
            ->orderBy('mass_order')
            ->get()
            ->groupBy('mass_order');
            
        $specialMasses = MassSchedule::where('type', 'special_mass')
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.mass-schedules.index', compact('sundaySchedules', 'specialMasses'));
    }

    /**
     * Show the form for creating a new mass schedule.
     */
    public function create()
    {
        return view('admin.mass-schedules.create');
    }

    /**
     * Store a newly created mass schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sunday_mass,special_mass',
            'mass_order' => $request->type === 'sunday_mass' ? 'required|in:first,second,third,fourth' : 'nullable',
            'event_name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required|after:attendance_start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        // Create event first
        $event = Event::create([
            'name' => $validated['event_name'],
            'date' => $validated['date'],
            'time' => $validated['start_time'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'],
            'is_active' => true,
        ]);
        
        // Create mass schedule
        $massSchedule = MassSchedule::create([
            'event_id' => $event->id,
            'type' => $validated['type'],
            'mass_order' => $validated['mass_order'] ?? 'special',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'attendance_start_time' => $validated['attendance_start_time'],
            'attendance_end_time' => $validated['attendance_end_time'],
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.mass-schedules.index')
            ->with('success', 'Mass schedule created successfully.');
    }

    /**
     * Display the specified mass schedule.
     */
    public function show(MassSchedule $massSchedule)
    {
        $massSchedule->load('event');
        return view('admin.mass-schedules.show', compact('massSchedule'));
    }

    /**
     * Show the form for editing the specified mass schedule.
     */
    public function edit(MassSchedule $massSchedule)
    {
        $massSchedule->load('event');
        return view('admin.mass-schedules.edit', compact('massSchedule'));
    }

    /**
     * Update the specified mass schedule in storage.
     */
    public function update(Request $request, MassSchedule $massSchedule)
    {
        $validated = $request->validate([
            'type' => 'required|in:sunday_mass,special_mass',
            'mass_order' => $request->type === 'sunday_mass' ? 'required|in:first,second,third,fourth' : 'nullable',
            'event_name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required|after:attendance_start_time',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        // Update event
        $massSchedule->event->update([
            'name' => $validated['event_name'],
            'date' => $validated['date'],
            'time' => $validated['start_time'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'],
        ]);
        
        // Update mass schedule
        $massSchedule->update([
            'type' => $validated['type'],
            'mass_order' => $validated['mass_order'] ?? 'special',
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'attendance_start_time' => $validated['attendance_start_time'],
            'attendance_end_time' => $validated['attendance_end_time'],
        ]);
        
        return redirect()->route('admin.mass-schedules.index')
            ->with('success', 'Mass schedule updated successfully.');
    }

    /**
     * Remove the specified mass schedule from storage.
     */
    public function destroy(MassSchedule $massSchedule)
    {
        // Delete the associated event first (will cascade to mass schedule)
        $massSchedule->event->delete();
        
        return redirect()->route('admin.mass-schedules.index')
            ->with('success', 'Mass schedule deleted successfully.');
    }
    
    /**
     * Create or update Sunday mass schedules.
     */
    public function setupSundayMasses(Request $request)
    {
        $validated = $request->validate([
            'first_mass_start' => 'required',
            'first_mass_end' => 'required|after:first_mass_start',
            'first_mass_attendance_start' => 'required',
            'first_mass_attendance_end' => 'required|after:first_mass_attendance_start',
            
            'second_mass_start' => 'required',
            'second_mass_end' => 'required|after:second_mass_start',
            'second_mass_attendance_start' => 'required',
            'second_mass_attendance_end' => 'required|after:second_mass_attendance_start',
            
            'third_mass_start' => 'required',
            'third_mass_end' => 'required|after:third_mass_start',
            'third_mass_attendance_start' => 'required',
            'third_mass_attendance_end' => 'required|after:third_mass_attendance_start',
            
            'fourth_mass_start' => 'required',
            'fourth_mass_end' => 'required|after:fourth_mass_start',
            'fourth_mass_attendance_start' => 'required',
            'fourth_mass_attendance_end' => 'required|after:fourth_mass_attendance_start',
        ]);
        
        // Define the mass orders and their corresponding fields
        $massOrders = [
            'first' => [
                'start' => 'first_mass_start',
                'end' => 'first_mass_end',
                'attendance_start' => 'first_mass_attendance_start',
                'attendance_end' => 'first_mass_attendance_end',
            ],
            'second' => [
                'start' => 'second_mass_start',
                'end' => 'second_mass_end',
                'attendance_start' => 'second_mass_attendance_start',
                'attendance_end' => 'second_mass_attendance_end',
            ],
            'third' => [
                'start' => 'third_mass_start',
                'end' => 'third_mass_end',
                'attendance_start' => 'third_mass_attendance_start',
                'attendance_end' => 'third_mass_attendance_end',
            ],
            'fourth' => [
                'start' => 'fourth_mass_start',
                'end' => 'fourth_mass_end',
                'attendance_start' => 'fourth_mass_attendance_start',
                'attendance_end' => 'fourth_mass_attendance_end',
            ],
        ];
        
        // Process each mass order
        foreach ($massOrders as $order => $fields) {
            // Find or create the mass schedule
            $massSchedule = MassSchedule::firstOrNew([
                'type' => 'sunday_mass',
                'mass_order' => $order,
            ]);
            
            if (!$massSchedule->exists) {
                // Create a new event for this mass schedule
                $event = Event::create([
                    'name' => ucfirst($order) . ' Sunday Mass',
                    'date' => Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d'),
                    'time' => $validated[$fields['start']],
                    'description' => 'Regular Sunday Mass',
                    'location' => 'Church',
                    'is_active' => true,
                ]);
                
                $massSchedule->event_id = $event->id;
            } else {
                // Update the existing event
                $massSchedule->event->update([
                    'time' => $validated[$fields['start']],
                ]);
            }
            
            // Update the mass schedule
            $massSchedule->start_time = $validated[$fields['start']];
            $massSchedule->end_time = $validated[$fields['end']];
            $massSchedule->attendance_start_time = $validated[$fields['attendance_start']];
            $massSchedule->attendance_end_time = $validated[$fields['attendance_end']];
            $massSchedule->is_active = true;
            $massSchedule->save();
        }
        
        return redirect()->route('admin.mass-schedules.index')
            ->with('success', 'Sunday mass schedules updated successfully.');
    }
}
