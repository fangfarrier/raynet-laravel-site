@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <h1>Admin login</h1>

    <p style="color:#9ca3af; max-width:32rem; margin-top:0.5rem;">
        This area is for Liverpool RAYNET officers to manage the events calendar.
    </p>

    @if ($errors->any())
        <div style="margin-top:0.75rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    border:1px solid rgba(248,113,113,0.8); background:rgba(127,29,29,0.8); color:#fecaca;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="post" action="{{ route('admin.login.submit') }}" style="margin-top:1.25rem; max-width:20rem;">
        @csrf

        <label for="password" style="display:block; font-size:0.9rem; margin-bottom:0.25rem;">
            Admin password
        </label>
        <input id="password" name="password" type="password"
               style="width:100%; padding:0.45rem; border-radius:0.4rem;
                      border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">

        <button type="submit"
                style="margin-top:0.9rem; padding:0.45rem 1.1rem; border-radius:999px;
                       border:1px solid rgba(56,189,248,0.9); background:#020617; color:#e5e7eb;">
            Log in
        </button>
    </form>
@endsection
