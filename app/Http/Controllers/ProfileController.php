<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

// ✅ REQUIRED: bring in the validation rules for profile update
use App\Http\Requests\ProfileUpdateRequest;

// ✅ REQUIRED: used to sync user → operator record
use App\Models\Operator;

class ProfileController extends Controller
{
    /**
     * Show the "My profile" page for the currently logged-in user.
     */
    public function edit(Request $request): View
    {
        // Grab the authenticated user
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Handle profile updates (name, email, callsign).
     *
     * IMPORTANT:
     * - We now allow members to change email.
     * - This uses ProfileUpdateRequest for validation.
     * - Callsign is synced to the Operator record.
     * - Email changes reset email_verified_at as expected in Laravel.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user     = $request->user();
        $oldEmail = $user->email;

        // Apply validated fields (name, email, callsign)
        // fill() automatically merges only validated attributes
        $user->fill($request->validated());

        // If email changed → force re-verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;   // reset verification
        }

        $user->save();

        // Keep Operator record in sync if one exists
        // We match first on the OLD email then on callsign.
        Operator::where('email', $oldEmail)
            ->orWhere('callsign', $user->callsign)
            ->update([
                'name'     => $user->name,
                'email'    => $user->email,
                'callsign' => $user->callsign,
            ]);

        // Standard Laravel profile-updated banner
        return back()->with('status', 'profile-updated');
    }
}