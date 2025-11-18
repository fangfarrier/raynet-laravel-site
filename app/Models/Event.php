<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'location',
        'starts_at',
        'ends_at',
        'category',
        'description',
        'event_type_id',
        'is_public',
    ];

    protected $casts = [
        'starts_at'   => 'datetime',
        'ends_at'     => 'datetime',
        'is_public'   => 'boolean',
    ];

    /**
     * Relationship: each event belongs to one event type.
     */
    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    /**
     * Nice human-readable event date / date range.
     */
    public function displayDate(): string
    {
        if (! $this->starts_at) {
            return '';
        }

        $start = $this->starts_at;
        $end   = $this->ends_at;

        // No end time: simple "D j M Y, H:i"
        if (! $end) {
            return $start->format('D j M Y, H:i');
        }

        // Same calendar day: "D j M Y, H:i–H:i"
        if ($start->toDateString() === $end->toDateString()) {
            return sprintf(
                '%s, %s–%s',
                $start->format('D j M Y'),
                $start->format('H:i'),
                $end->format('H:i'),
            );
        }

        // Multi-day event: "D j M Y, H:i – D j M Y, H:i"
        return sprintf(
            '%s – %s',
            $start->format('D j M Y, H:i'),
            $end->format('D j M Y, H:i'),
        );
    }

    /**
     * Generate the public-facing event URL.
     */
    public function url(): string
    {
        if (! $this->starts_at || ! $this->slug) {
            return '#';
        }

        $year  = $this->starts_at->format('Y');
        $month = $this->starts_at->format('m');

        return route('events.show', [
            'year'  => $year,
            'month' => $month,
            'slug'  => $this->slug,
        ]);
    }
}