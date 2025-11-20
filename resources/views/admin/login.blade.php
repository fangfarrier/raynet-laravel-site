@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div style="max-width: 420px; margin: 0 auto;">
        <h1 style="font-size: 1.3rem; margin-bottom: 0.25rem;">Admin login</h1>
        <p style="font-size: 0.85rem; color: #9ca3af; margin-bottom: 1rem;">
            Restricted access for Liverpool RAYNET controllers and administrators.
            Use your email address or callsign plus password.
        </p>

        @if ($errors->has('login'))
            <div style="
                padding: 0.6rem 0.8rem;
                border-radius: 0.5rem;
                background: rgba(220, 38, 38, 0.12);
                border: 1px solid rgba(248, 113, 113, 0.6);
                color: #fecaca;
                font-size: 0.85rem;
                margin-bottom: 0.9rem;
            ">
                {{ $errors->first('login') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @csrf

            <div>
                <label for="login" style="display:block; font-size:0.85rem; margin-bottom:0.25rem;">
                    Email or callsign
                </label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    value="{{ old('login') }}"
                    required
                    autofocus
                    style="
                        width: 100%;
                        padding: 0.45rem 0.55rem;
                        border-radius: 0.35rem;
                        border: 1px solid #4b5563;
                        background: #020617;
                        color: #e5e7eb;
                    "
                >
            </div>

            <div>
                <label for="password" style="display:block; font-size:0.85rem; margin-bottom:0.25rem;">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    style="
                        width: 100%;
                        padding: 0.45rem 0.55rem;
                        border-radius: 0.35rem;
                        border: 1px solid #4b5563;
                        background: #020617;
                        color: #e5e7eb;
                    "
                >
            </div>

            <button type="submit" style="
                margin-top: 0.5rem;
                padding: 0.5rem 0.75rem;
                border-radius: 999px;
                border: none;
                background: linear-gradient(to right, #4f46e5, #7c3aed);
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
            ">
                Sign in
            </button>
        </form>

        <p style="margin-top: 0.9rem; font-size: 0.75rem; color: #6b7280;">
            Access is limited to designated Liverpool RAYNET officers.
            All activity may be logged for governance and audit.
        </p>
    </div>
@endsection