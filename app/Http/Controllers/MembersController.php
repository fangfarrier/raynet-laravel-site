<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Carbon;

class MembersController extends Controller
{
    /**
     * Reminder to self: Members' hub – show upcoming events and useful links.
     */
    public function index()
    {
        $today = Carbon::today();

        $upcoming = Event::with('type')
            ->where('starts_at', '>=', $today->startOfDay())
            ->orderBy('starts_at')
            ->limit(6)
            ->get();

        return view('pages.members', [
            'upcoming' => $upcoming,
        ]);
    }
}
