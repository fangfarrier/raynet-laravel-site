<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SimpleAuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle the login attempt.
     *
     * Users can log in with either email address or callsign, case-insensitive.
     */
    public function processLogin(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Normalise the login string (email OR callsign) to lower-case
        $login = strtolower(trim($data['login']));

        // Look up by email OR callsign, case-insensitive
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$login])
            ->orWhereRaw('LOWER(callsign) = ?', [$login])
            ->first();

        // Check password
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors([
                    'login' => 'Invalid credentials or no account found.',
                ]);
        }

        // Log them in
        Auth::login($user, $request->boolean('remember'));

        // Update password meta (used by your ForcePasswordChange middleware)
        $user->force_password_reset = false;
        $user->password_changed_at = now();
        $user->save();

        // Regenerate session
        $request->session()->regenerate();

        // Redirect to intended page (members’ hub by default)
        return redirect()->intended(route('members'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}