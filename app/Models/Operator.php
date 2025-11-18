<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    // Fields I want to be able to mass-assign
    protected $fillable = [
        'name',
        'callsign',
        'role',
        'level',
        'status',
        'joined_at',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    // Helper for the combined “role / level” label on the badge
    public function displayLevel(): string
    {
        if ($this->role && $this->level) {
            return $this->role . ' / ' . $this->level;
        }

        if ($this->role) {
            return $this->role;
        }

        return $this->level ?? '';
    }
}