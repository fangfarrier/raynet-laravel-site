<?php

namespace App\Http\Requests\Auth;

use App\Models\Operator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Login is open to guests
        return true;
    }

    public function rules(): array
    {
        return [
            // Single field for either email OR callsign
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Perform the actual authentication attempt.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login    = trim((string) $this->input('login'));
        $password = (string) $this->input('password');

        // Decide whether this is an email or a callsign
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Email login
            $credentials = [
                'email'    => $login,
                'password' => $password,
            ];
        } else {
            // Treat as callsign – look up operator, grab their email
            $operator = Operator::whereRaw('upper(callsign) = ?', [strtoupper($login)])->first();

            if (! $operator || empty($operator->email)) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => trans('auth.failed'),
                ]);
            }

            $credentials = [
                'email'    => $operator->email,
                'password' => $password,
            ];
        }

        // Attempt login against users table
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Basic rate limiting – same as Breeze default, but keyed on 'login'.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->input('login')).'|'.$this->ip();
    }
}