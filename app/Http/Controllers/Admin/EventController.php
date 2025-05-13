<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $event = Event::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load('attendances.user');
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $event->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        // Check if there are attendances associated with this event
        if ($event->attendances()->count() > 0) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Cannot delete event with existing attendances.');
        }
        
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
