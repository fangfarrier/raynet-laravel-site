<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        return view('admin.login');
    }

    /**
     * Handle the login POST.
     */
    public function login(Request $request)
    {
        // Simple password-only gate, no users table
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $expected = env('ADMIN_PASSWORD');

        if (! $expected || $request->password !== $expected) {
            return back()
                ->withErrors(['password' => 'Incorrect admin password.'])
                ->withInput();
        }

        // Mark this session as "admin"
        session(['is_admin' => true]);

        return redirect()->route('admin.events');
    }

    /**
     * Log out and clear admin session.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}