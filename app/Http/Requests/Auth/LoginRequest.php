<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     *
     * NOTE: We keep the input field name as "email" for compatibility
     * with the existing login blade, but it can now contain either an
     * email address OR a callsign.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string'], // no longer enforcing "email" format
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Allows login by email OR callsign, case-insensitive.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login    = strtolower((string) $this->input('email'));   // email OR callsign, lowercased
        $password = (string) $this->input('password');

        // Try to find user by email OR callsign (both case-insensitive)
        $user = User::query()
            ->whereRaw('lower(email) = ?', [$login])
            ->orWhereRaw('lower(callsign) = ?', [$login])
            ->first();

        // If no user or password mismatch, fail + increment rate limiter
        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Successful login
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')) . '|' . $this->ip());
    }
}
