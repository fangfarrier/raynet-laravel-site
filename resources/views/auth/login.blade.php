@extends('layouts.app')

@section('title', 'Member login')

@section('content')
    <section style="
        max-width: 420px;
        margin: 2rem auto;
        padding: 1.8rem 1.6rem 1.6rem;
        border-radius: 1.2rem;
        border: 1px solid rgba(148,163,184,0.5);
        background: rgba(15,23,42,0.96);
        box-shadow: 0 20px 45px rgba(15,23,42,0.85);
        font-size: 0.9rem;
        color: #e5e7eb;
    ">
        <h1 style="margin:0 0 0.6rem; font-size:1.15rem;">
            Member login
        </h1>
        <p style="margin:0 0 1rem; font-size:0.8rem; color:#9ca3af;">
            Log in with <strong>either your email address or callsign</strong>.
            The check is case-insensitive (g4bds, G4BDS, g4Bds all work).
        </p>

        @if($errors->any())
            <div style="
                margin-bottom:0.9rem;
                padding:0.55rem 0.75rem;
                border-radius:0.7rem;
                border:1px solid rgba(239,68,68,0.8);
                background:rgba(127,29,29,0.4);
                font-size:0.8rem;
                color:#fee2e2;
            ">
                {{ $errors->first('login') ?? 'Login problem – please check your details and try again.' }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:0.75rem;">
            @csrf

            <div>
                <label for="login" style="display:block; font-size:0.8rem; margin-bottom:0.2rem; color:#cbd5f5;">
                    Email address <em>or</em> callsign
                </label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    autocomplete="username"
                    value="{{ old('login') }}"
                    required
                    style="
                        width:100%;
                        padding:0.5rem 0.65rem;
                        border-radius:0.5rem;
                        border:1px solid rgba(148,163,184,0.6);
                        background:#020617;
                        color:#e5e7eb;
                        font-size:0.9rem;
                    "
                >
            </div>

            <div>
                <label for="password" style="display:block; font-size:0.8rem; margin-bottom:0.2rem; color:#cbd5f5;">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    style="
                        width:100%;
                        padding:0.5rem 0.65rem;
                        border-radius:0.5rem;
                        border:1px solid rgba(148,163,184,0.6);
                        background:#020617;
                        color:#e5e7eb;
                        font-size:0.9rem;
                    "
                >
            </div>

            <label style="display:flex; align-items:center; gap:0.35rem; font-size:0.78rem; color:#9ca3af;">
                <input type="checkbox" name="remember" style="accent-color:#38bdf8;">
                Remember me on this device
            </label>

            <button type="submit" style="
                margin-top:0.4rem;
                width:100%;
                padding:0.55rem 0.75rem;
                border-radius:999px;
                border:none;
                background:linear-gradient(to right,#38bdf8,#2563eb);
                color:#0f172a;
                font-weight:700;
                font-size:0.9rem;
                cursor:pointer;
            ">
                Log in
            </button>

            <p style="margin:0.7rem 0 0; font-size:0.72rem; color:#64748b;">
                Access is limited to Liverpool RAYNET members. All activity may be logged for governance and audit.
            </p>
        </form>
    </section>
@endsection