<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Handle admin login – email OR callsign.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login    = trim($request->input('login'));
        $password = $request->input('password');

        $credentials = null;

        // If it looks like an email, use it directly.
        if (str_contains($login, '@')) {
            $credentials = [
                'email'    => $login,
                'password' => $password,
            ];
        } else {
            // Treat as callsign – map to operator, then to user's email
            $operator = Operator::where('callsign', strtoupper($login))->first();

            if (! $operator || ! $operator->email) {
                return back()
                    ->withErrors(['login' => 'Invalid credentials or no admin access for this account.'])
                    ->withInput($request->only('login'));
            }

            $credentials = [
                'email'    => $operator->email,
                'password' => $password,
            ];
        }

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['login' => 'Invalid credentials or no admin access for this account.'])
                ->withInput($request->only('login'));
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only allow through if this user is flagged as admin
        if (! $user->is_admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login' => 'Invalid credentials or no admin access for this account.'])
                ->withInput($request->only('login'));
        }

        // Mark this session as admin for the pill and /admin middleware
        session(['is_admin' => true]);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget('is_admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}