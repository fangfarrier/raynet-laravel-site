@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')
<div style="max-width:600px; margin:0 auto; padding:1rem;">

    <h1 style="color:#e5e7eb; margin-bottom:1.5rem;">Admin dashboard</h1>

    <div style="display:flex; flex-direction:column; gap:0.75rem;">

        <a href="{{ route('admin.events') }}"
            style="padding:0.8rem 1rem; border-radius:0.6rem;
                   border:1px solid rgba(148,163,184,0.5);
                   background:#0f172a; color:#e5e7eb;
                   text-decoration:none; display:block;">
            📅 Manage events
        </a>

        <a href="{{ route('admin.event-types') }}"
            style="padding:0.8rem 1rem; border-radius:0.6rem;
                   border:1px solid rgba(148,163,184,0.5);
                   background:#0f172a; color:#e5e7eb;
                   text-decoration:none; display:block;">
            🏷 Manage event types (colours)
        </a>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                style="padding:0.8rem 1rem; border-radius:0.6rem;
                       border:1px solid rgba(239,68,68,0.6);
                       background:#1e293b; color:#fca5a5;
                       width:100%; cursor:pointer;">
                Log out
            </button>
        </form>

    </div>

</div>
@endsection