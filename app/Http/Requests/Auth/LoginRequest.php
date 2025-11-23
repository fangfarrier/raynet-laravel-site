<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Everyone can hit the login form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the login form.
     *
     * NOTE:
     * - The field on the form is still called "email"
     *   but the label says "Email address or callsign".
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string'], // email OR callsign
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt authentication using either email OR callsign,
     * based on what the user typed into the "email" box.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // What the user actually typed in the login box
        $login = $this->input('email');

        // Work out whether it's an email or a callsign
        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'callsign';

        // Build the credentials array for Auth::attempt
        $credentials = [
            $field     => $login,
            'password' => $this->input('password'),
        ];

        // "Remember me" checkbox
        $remember = $this->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            // Attach the error message to the "email" field so the
            // red bar appears where you'd expect on the form.
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Throttle key – same as Laravel’s default, but we still
     * treat it as "email" for the purposes of rate limiting.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}