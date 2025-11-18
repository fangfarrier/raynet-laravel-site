<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventTypeAdminController extends Controller
{
    public function index()
    {
        $types = EventType::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.event-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'colour'     => ['nullable', 'string', 'max:7'], // hex like "#2563eb"
        ]);

        // Normalise colour to "#RRGGBB" if provided
        $colour = $data['colour'] ?? null;

        if ($colour !== null && $colour !== '') {
            // strip leading #, cap to 6 chars, then put # back
            $colour = ltrim($colour, '#');
            $colour = substr($colour, 0, 6);
            $colour = '#' . $colour;
        } else {
            $colour = null;
        }

        EventType::create([
            'name'       => $data['name'],
            'slug'       => Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? 0,
            'colour'     => $colour,
        ]);

        return redirect()
            ->route('admin.event-types')
            ->with('status', 'Event type created.');
    }

    public function delete(int $id)
    {
        $type = EventType::findOrFail($id);

        // Optional safety: only allow delete if no events use it
        if ($type->events()->exists()) {
            return redirect()
                ->route('admin.event-types')
                ->with('status', 'Cannot delete – there are events using this type.');
        }

        $type->delete();

        return redirect()
            ->route('admin.event-types')
            ->with('status', 'Event type deleted.');
    }
}