<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

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
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_public' => 'boolean',
    ];

    /**
     * Relationship: each event belongs to one event type.
     */
    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    /**
     * Nice human-readable event date.
     */
    public function displayDate(): string
    {
        if (! $this->starts_at) {
            return '';
        }

        // Example: Sun 23 Nov 2025, 08:30
        return $this->starts_at->format('D j M Y, H:i');
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