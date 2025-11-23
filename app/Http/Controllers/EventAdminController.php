<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * EventAdminController
 *
 * This controller handles ALL admin-facing event management:
 *  - Listing events (with pagination)
 *  - Creating events
 *  - Deleting events
 *  - Exporting events to CSV (backup / move to MySQL)
 *  - Importing events from CSV (restore / bulk load)
 *
 * IMPORTANT: If you ever need to tweak columns, slugs or types,
 * look for the big section headers:
 *   [INDEX], [CREATE], [DELETE], [EXPORT], [IMPORT]
 */
class EventAdminController extends Controller
{
    // ---------------------------------------------------------------------
    // [INDEX] – Show the admin events list with pagination
    // ---------------------------------------------------------------------

    /**
     * Display the admin event list (with add form + pagination).
     */
    public function index(): View
    {
        // We show most recent events first and paginate to avoid huge tables.
        $events = Event::with('type')
            ->orderBy('starts_at', 'desc')
            ->paginate(10); // 10 per page; tweak here if you want more/less

        // Event types are used for the "Event type" dropdown in the add form.
        $types = EventType::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.events.index', compact('events', 'types'));
    }

    // ---------------------------------------------------------------------
    // [CREATE] – Add a new event from the admin form
    // ---------------------------------------------------------------------

    /**
     * Create a new event from the admin "Add event" form.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validate the incoming data from the form.
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'location'      => ['nullable', 'string', 'max:255'],
            'starts_at'     => ['required', 'date'],
            'ends_at'       => ['nullable', 'date', 'after:starts_at'],
            'event_type_id' => ['required', 'exists:event_types,id'],
            'description'   => ['nullable', 'string'],
        ], [], [
            // Friendly field names for error messages
            'starts_at'     => 'start date/time',
            'ends_at'       => 'end date/time',
            'event_type_id' => 'event type',
        ]);

        // 2) Build a new Event record.
        $event = new Event;

        $event->title         = $data['title'];
        $event->slug          = Str::slug($data['title']); // URL slug, also used in import/export
        $event->location      = $data['location'] ?? null;
        $event->starts_at     = Carbon::parse($data['starts_at']);
        $event->ends_at       = !empty($data['ends_at'])
            ? Carbon::parse($data['ends_at'])
            : null;
        $event->event_type_id = $data['event_type_id'];
        $event->description   = $data['description'] ?? null;

        // By default, admin-created events are public.
        $event->is_public     = true;

        $event->save();

        return redirect()
            ->route('admin.events')
            ->with('status', 'Event created.');
    }

    // ---------------------------------------------------------------------
    // [DELETE] – Remove an event
    // ---------------------------------------------------------------------

    /**
     * Delete an event by ID.
     */
    public function delete(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()
            ->route('admin.events')
            ->with('status', 'Event deleted.');
    }

    // ---------------------------------------------------------------------
    // [EXPORT] – Stream all events out as CSV
    // ---------------------------------------------------------------------

    /**
     * Export all events as a CSV file.
     *
     * NOTE TO FUTURE IAN:
     * - This is the CANONICAL definition of the CSV format used by both
     *   export() and import().
     * - If you add/remove columns here, you MUST update:
     *     1) The header row below
     *     2) The fputcsv() row builder
     *     3) The $required columns list in import()
     */
    public function export(): StreamedResponse
    {
        // Build a sensible filename with a timestamp so downloads don't clash.
        $fileName = 'events_export_' . now()->format('Y-m-d_His') . '.csv';

        // HTTP headers that tell the browser "this is a CSV download".
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        // The callback actually writes the CSV to php://output as the response streams.
        $callback = function () {
            // Open a writeable "file" handle to the HTTP response body.
            $handle = fopen('php://output', 'w');

            // 1) HEADER ROW – keep this in sync with import()'s $required list.
            fputcsv($handle, [
                'id',           // optional – used just for reference
                'title',
                'slug',
                'starts_at',
                'ends_at',
                'location',
                'type_name',    // text name of the type, eg "Training"
                'description',
                'is_sample',    // 0/1 flag so you can mark seed data
                'is_public',    // 0/1 flag
                'created_at',
                'updated_at',
            ]);

            // 2) Stream events in chunks so we never load the entire table into memory.
            Event::with('type')
                ->orderBy('starts_at')
                ->chunk(200, function ($events) use ($handle) {
                    /** @var \App\Models\Event $event */
                    foreach ($events as $event) {
                        fputcsv($handle, [
                            $event->id,
                            $event->title,
                            $event->slug,
                            optional($event->starts_at)->format('Y-m-d H:i:s'),
                            optional($event->ends_at)->format('Y-m-d H:i:s'),
                            $event->location,
                            optional($event->type)->name,    // e.g. "Training", "Exercise"
                            $event->description,
                            $event->is_sample ? 1 : 0,
                            $event->is_public ? 1 : 0,
                            optional($event->created_at)->format('Y-m-d H:i:s'),
                            optional($event->updated_at)->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            // Always close handles you open, even for php://output.
            fclose($handle);
        };

        // Return a streamed response object Laravel understands.
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Backwards-compatible alias so both:
     *   /admin/events/export
     * and
     *   /admin/events/export/csv
     * can share the same export logic.
     *
     * Routes:
     *   - admin.events.export      -> export()
     *   - admin.events.export.csv  -> exportCsv() -> export()
     */
    public function exportCsv(): StreamedResponse
    {
        return $this->export();
    }

    // ---------------------------------------------------------------------
    // [IMPORT] – Show form + process uploaded CSV
    // ---------------------------------------------------------------------

    /**
     * Show the CSV import form.
     *
     * View:
     *   resources/views/admin/events/import.blade.php
     *
     * That view uses route('admin.events.import') as its POST target.
     */
    public function showImportForm(): View
    {
        return view('admin.events.import');
    }

    /**
     * Handle CSV upload and import events.
     *
     * SAFETY RULES:
     * - Validates file as CSV.
     * - Maps columns by header name (case-insensitive).
     * - By default, existing events (same slug) are *skipped*.
     * - If "update_existing" checkbox is ticked, existing records are updated.
     *
     * CANONICAL COLUMN SET:
     *   id, title, slug, starts_at, ends_at, location,
     *   type_name, description, is_sample, is_public,
     *   created_at, updated_at
     *
     * Only some of those are actually used on import, but we require:
     *   title, slug, starts_at, location, type_name, description, is_sample
     */
    public function import(Request $request): RedirectResponse
    {
        // 1) Basic file validation.
        $data = $request->validate([
            'events_file'      => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // ~5MB
            'update_existing'  => ['nullable', 'boolean'],
        ]);

        $updateExisting = (bool)($data['update_existing'] ?? false);

        $file = $request->file('events_file');
        $path = $file->getRealPath();

        if (! $path || ! file_exists($path)) {
            return back()->withErrors(['events_file' => 'Uploaded file could not be read.']);
        }

        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->withErrors(['events_file' => 'Unable to open the uploaded file.']);
        }

        // 2) Read header row and normalise column names.
        $header = fgetcsv($handle);

        if (! $header) {
            fclose($handle);
            return back()->withErrors(['events_file' => 'CSV file appears to be empty or invalid.']);
        }

        // Map: lowercased header => index
        $map = [];
        foreach ($header as $index => $name) {
            $key = strtolower(trim($name));
            if ($key !== '') {
                $map[$key] = $index;
            }
        }

        // Columns we expect (based on our export() header row above).
        // If you add/remove columns there, update this list too.
        $required = [
            'title',
            'slug',
            'starts_at',
            'ends_at',
            'location',
            'type_name',
            'description',
            'is_sample',
        ];

        foreach ($required as $column) {
            if (! array_key_exists($column, $map)) {
                fclose($handle);
                return back()->withErrors([
                    'events_file' =>
                        "Missing required column '{$column}' in header row. " .
                        "Tip: export a CSV first, edit it, then re-import.",
                ]);
            }
        }

        // 3) Process rows.
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $line    = 1; // we've already read the header

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            // Skip completely blank lines.
            if (count(array_filter($row, fn ($v) => trim((string)$v) !== '')) === 0) {
                continue;
            }

            // Helper closure to grab a column by canonical name.
            $col = function (string $name) use ($map, $row): ?string {
                $index = $map[$name] ?? null;
                if ($index === null || ! array_key_exists($index, $row)) {
                    return null;
                }
                return trim((string)$row[$index]);
            };

            $title       = $col('title') ?: null;
            $slug        = $col('slug') ?: null;
            $startsAtRaw = $col('starts_at') ?: null;
            $endsAtRaw   = $col('ends_at') ?: null;
            $location    = $col('location') ?: null;
            $typeName    = $col('type_name') ?: null;
            $description = $col('description') ?: null;
            $isSampleRaw = strtolower($col('is_sample') ?? '');

            // Basic mandatory checks.
            if (! $title || ! $slug || ! $startsAtRaw) {
                $skipped++;
                continue;
            }

            // Parse start date/time carefully.
            try {
                $startsAt = Carbon::parse($startsAtRaw);
            } catch (\Throwable $e) {
                $skipped++;
                continue;
            }

            // End date/time is optional; bad values are dropped to null.
            $endsAt = null;
            if (! empty($endsAtRaw)) {
                try {
                    $endsAt = Carbon::parse($endsAtRaw);
                } catch (\Throwable $e) {
                    $endsAt = null;
                }
            }

            // Treat various truthy-ish strings as "sample data".
            $isSample = in_array($isSampleRaw, ['1', 'true', 'yes', 'y'], true);

            // Find or create event type by *name* (text).
            $eventTypeId = null;
            if ($typeName) {
                $type = EventType::firstOrCreate(
                    ['name' => $typeName],
                    ['colour' => '#22c55e'] // default colour if new
                );
                $eventTypeId = $type->id;
            }

            // Look for existing event by slug.
            $existing = Event::where('slug', $slug)->first();

            if ($existing) {
                if (! $updateExisting) {
                    // Admin didn't tick "update existing" – leave record alone.
                    $skipped++;
                    continue;
                }

                // Update existing event.
                $existing->title          = $title;
                $existing->starts_at      = $startsAt;
                $existing->ends_at        = $endsAt;
                $existing->location       = $location;
                $existing->description    = $description;
                $existing->is_sample      = $isSample;
                $existing->event_type_id  = $eventTypeId;

                $existing->save();
                $updated++;
            } else {
                // Create a new event.
                Event::create([
                    'title'         => $title,
                    'slug'          => $slug,
                    'starts_at'     => $startsAt,
                    'ends_at'       => $endsAt,
                    'location'      => $location,
                    'description'   => $description,
                    'is_sample'     => $isSample,
                    'event_type_id' => $eventTypeId,
                    // is_public can be defaulted in the migration/model if you want
                    // 'is_public' => true,
                ]);

                $created++;
            }
        }

        fclose($handle);

        // 4) Return with a status summary.
        $message = sprintf(
            'Events import complete: %d created, %d updated, %d skipped.',
            $created,
            $updated,
            $skipped
        );

        // Take admin back to the events list with the summary.
        return redirect()
            ->route('admin.events')
            ->with('status', $message);
    }
}