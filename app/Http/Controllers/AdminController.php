<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     *
     * NOTE:
     * - If an admin user is already logged in, we *skip* the login form
     *   and send them straight to the admin dashboard.
     */
    public function showLoginForm()
    {
        // If already logged in *and* an admin, don't show the login form again.
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle the admin login POST.
     *
     * This does NOT use the normal LoginRequest so it won't interfere with
     * the members' login at /login. It is completely self-contained.
     */
    public function login(Request $request)
    {
        // 1) Basic validation – make sure both fields are present
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');

        // 2) Work out whether the user typed an email or a callsign
        if (str_contains($login, '@')) {
            // Email login (exact match)
            $user = User::where('email', $login)->first();
        } else {
            // Callsign login – case-insensitive
            $upper = Str::upper($login);
            $user  = User::whereRaw('UPPER(callsign) = ?', [$upper])->first();
        }

        // 3) No user found
        if (! $user) {
            return back()
                ->withErrors([
                    'login' => 'No matching admin account was found for that email or callsign.',
                ])
                ->withInput($request->only('login'));
        }

        // 4) User exists but is not an admin
        if (! $user->is_admin) {
            return back()
                ->withErrors([
                    'login' => 'This account does not have admin access.',
                ])
                ->withInput($request->only('login'));
        }

        // 5) Password check
        if (! Hash::check($password, $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'Incorrect password – please try again.',
                ])
                ->withInput($request->only('login'));
        }

        // 6) All good – log them in on the default (web) guard
        Auth::login($user);
        $request->session()->regenerate();

        // Optional: flash a quick banner so we *know* this code path ran
        session()->flash('status', 'Admin login OK.');

        // 7) Send them to the admin dashboard
        return redirect()->route('/admin');
    }

    /**
     * Admin logout – clears session and returns to admin login form.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}