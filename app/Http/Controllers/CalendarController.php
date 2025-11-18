<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    /**
     * Month view calendar.
     */
    public function index(?int $year = null, ?int $month = null)
    {
        // Work out which month we’re showing
        $today = Carbon::today();

        if ($year === null || $month === null) {
            $currentMonth = $today->copy()->startOfMonth();
        } else {
            $currentMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        }

        $monthStart = $currentMonth->copy()->startOfMonth();
        $monthEnd   = $currentMonth->copy()->endOfMonth();

        // Grid runs from Monday at/before start, to Sunday at/after end
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd   = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        // Fetch all events that overlap this grid window
        $events = Event::with('type')
            ->where('starts_at', '<=', $gridEnd->copy()->endOfDay())
            ->where(function ($q) use ($gridStart) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $gridStart->copy()->startOfDay());
            })
            ->orderBy('starts_at')
            ->get();

        // Map events onto each day in the grid, including multi-day spans
        $eventsByDay = [];

        foreach ($events as $event) {
            $rangeStart = $event->starts_at->copy()->startOfDay();
            $rangeEnd   = $event->ends_at
                ? $event->ends_at->copy()->startOfDay()
                : $rangeStart->copy();

            // Clamp to the visible grid
            if ($rangeEnd < $gridStart || $rangeStart > $gridEnd) {
                continue;
            }

            if ($rangeStart < $gridStart) {
                $rangeStart = $gridStart->copy();
            }
            if ($rangeEnd > $gridEnd) {
                $rangeEnd = $gridEnd->copy();
            }

            $cursor = $rangeStart->copy();

            while ($cursor <= $rangeEnd) {
                $key = $cursor->toDateString();

                if ($rangeStart->equalTo($rangeEnd)) {
                    $span = 'single';
                } elseif ($cursor->equalTo($rangeStart)) {
                    $span = 'start';
                } elseif ($cursor->equalTo($rangeEnd)) {
                    $span = 'end';
                } else {
                    $span = 'middle';
                }

                $eventsByDay[$key][] = [
                    'event' => $event,
                    'span'  => $span,
                ];

                $cursor->addDay();
            }
        }

        // Build weeks → days structure for the view
        $weeks  = [];
        $cursor = $gridStart->copy();

        while ($cursor <= $gridEnd) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $date = $cursor->copy();
                $key  = $date->toDateString();

                $week[] = [
                    'date'          => $date,
                    'isCurrentMonth'=> $date->month === $currentMonth->month,
                    'isToday'       => $date->isSameDay($today),
                    'events'        => $eventsByDay[$key] ?? [],
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('calendar.index', [
            'currentMonth' => $currentMonth,
            'monthName'    => $currentMonth->format('F'),
            'year'         => $currentMonth->year,
            'weeks'        => $weeks,
            'prevYear'     => $prevMonth->year,
            'prevMonth'    => $prevMonth->month,
            'nextYear'     => $nextMonth->year,
            'nextMonth'    => $nextMonth->month,
        ]);
    }

    /**
     * Export the visible month as ICS.
     */
    public function ics(int $year, int $month)
    {
        $currentMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthStart   = $currentMonth->copy()->startOfMonth();
        $monthEnd     = $currentMonth->copy()->endOfMonth();

        $events = Event::with('type')
            ->whereBetween('starts_at', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->orderBy('starts_at')
            ->get();

        $siteName = config('app.name', 'Liverpool RAYNET');
        $domain   = parse_url(config('app.url', 'https://example.com'), PHP_URL_HOST) ?? 'example.com';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . $this->escapeIcsText($siteName) . '//EN',
            'CALSCALE:GREGORIAN',
        ];

        foreach ($events as $event) {
            $uid     = 'event-' . $event->id . '@' . $domain;
            $dtStart = $event->starts_at->copy()->utc()->format('Ymd\THis\Z');
            $dtEnd   = $event->ends_at
                ? $event->ends_at->copy()->utc()->format('Ymd\THis\Z')
                : $event->starts_at->copy()->addHours(2)->utc()->format('Ymd\THis\Z');
            $dtStamp = now()->utc()->format('Ymd\THis\Z');

            $summary = $event->title;

            $descriptionPieces = [];
            if ($event->description) {
                $descriptionPieces[] = $event->description;
            }
            if ($event->type) {
                $descriptionPieces[] = 'Type: ' . $event->type->name;
            }
            $description = implode('\n', $descriptionPieces);

            $location = $event->location ?? '';

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $this->escapeIcsText($uid);
            $lines[] = 'DTSTAMP:' . $dtStamp;
            $lines[] = 'DTSTART:' . $dtStart;
            $lines[] = 'DTEND:' . $dtEnd;
            $lines[] = 'SUMMARY:' . $this->escapeIcsText($summary);

            if ($location !== '') {
                $lines[] = 'LOCATION:' . $this->escapeIcsText($location);
            }
            if ($description !== '') {
                $lines[] = 'DESCRIPTION:' . $this->escapeIcsText($description);
            }

            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        $body = implode("\r\n", $lines) . "\r\n";

        $filename = sprintf('liverpool-raynet-%04d-%02d.ics', $year, $month);

        return response($body, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function escapeIcsText(string $text): string
    {
        $text = str_replace("\\", "\\\\", $text);
        $text = str_replace(";", "\;", $text);
        $text = str_replace(",", "\,", $text);
        $text = str_replace(["\r\n", "\r", "\n"], "\\n", $text);

        return $text;
    }
}