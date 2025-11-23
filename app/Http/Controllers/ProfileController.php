<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the "My profile" page for the currently logged-in user.
     *
     * Note to self:
     * - This uses $request->user() (same as Auth::user()).
     * - The route is protected by 'auth' middleware, so only logged-in members see it.
     */
    public function edit(Request $request): View
    {
        // Grab the current authenticated user
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Handle profile updates (name + callsign).
     *
     * Note to self:
     * - Callsign is:
     *      - optional (nullable)
     *      - must match /^[A-Z0-9\/]{3,10}$/i
     *      - unique across all users (db + validation)
     * - We normalise callsigns to UPPERCASE before saving.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validation rules for profile fields
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // Callsign is optional, regex enforced, and must be unique
            'callsign' => [
                'nullable',
                'string',
                'max:10',
                // Accept A–Z, 0–9 and /, 3–10 chars, case-insensitive
                'regex:/^[A-Z0-9\/]{3,10}$/i',
                // Unique in users table, but ignore this user’s own row
                'unique:users,callsign,' . $user->id,
            ],
        ], [
            // Nice readable error messages for callsign
            'callsign.regex'   => 'Callsign must be 3–10 characters of letters, numbers or "/".',
            'callsign.unique'  => 'That callsign is already in use by another account.',
        ]);

        // Normalise callsign to uppercase (if present)
        $callsign = $validated['callsign'] ?? null;
        if (!empty($callsign)) {
            $validated['callsign'] = strtoupper($callsign);
        }

        // Update the user in the database
        $user->fill([
            'name'     => $validated['name'],
            'callsign' => $validated['callsign'] ?? null,
        ]);

        $user->save();

        // Redirect back to /profile with a success flash message
        return redirect()
            ->route('profile.edit')
            ->with('status', 'Profile updated successfully.');
    }
}