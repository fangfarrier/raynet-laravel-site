<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventAdminController extends Controller
{
    /**
     * Display the admin event list.
     */
    public function index()
    {
        // Use pagination instead of get()
        //  - 10 events per page, newest first
        $events = Event::orderBy('starts_at', 'desc')
            ->paginate(10);

        // Fetch event types for the dropdown
        $types = EventType::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.events.index', compact('events', 'types'));
    }

    /**
     * Create a new event.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'location'      => ['nullable', 'string', 'max:255'],
            'starts_at'     => ['required', 'date'],
            'ends_at'       => ['nullable', 'date', 'after:starts_at'],
            'event_type_id' => ['required', 'exists:event_types,id'],
            'description'   => ['nullable', 'string'],
        ], [], [
            'starts_at'     => 'start date/time',
            'ends_at'       => 'end date/time',
            'event_type_id' => 'event type',
        ]);

        $event = new Event;

        $event->title         = $data['title'];
        $event->slug          = Str::slug($data['title']);
        $event->location      = $data['location'] ?? null;
        $event->starts_at     = Carbon::parse($data['starts_at']);
        $event->ends_at       = !empty($data['ends_at'])
            ? Carbon::parse($data['ends_at'])
            : null;
        $event->event_type_id = $data['event_type_id'];
        $event->description   = $data['description'] ?? null;
        $event->is_public     = true;

        $event->save();

        return redirect()
            ->route('admin.events')
            ->with('status', 'Event created.');
    }

    /**
     * Delete an event.
     */
    public function delete(int $id)
    {
        $event = Event::findOrFail($id);

        $event->delete();

        return redirect()
            ->route('admin.events')
            ->with('status', 'Event deleted.');
    }
}