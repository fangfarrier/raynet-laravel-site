@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')

    {{-- Top strip: who am I logged in as? --}}
    <section style="margin-top:1.5rem; margin-bottom:1.5rem;">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
        ">
            <div>
                <h1 style="margin:0 0 0.3rem;">Admin dashboard</h1>
                <p style="margin:0; font-size:0.9rem; color:#9ca3af;">
                    Central control panel for events, operators and roles.
                </p>
            </div>

            <div style="
                min-width: 230px;
                border-radius: 999px;
                padding: 0.45rem 0.9rem;
                background: radial-gradient(circle at top left,#4c1d95,#020617);
                border: 1px solid rgba(168,85,247,0.9);
                font-size: 0.78rem;
                color:#e5e7eb;
                display:flex;
                align-items:center;
                gap:0.5rem;
            ">
                <div style="
                    width: 9px;
                    height: 9px;
                    border-radius:999px;
                    background:#22c55e;
                    box-shadow:0 0 0 4px rgba(34,197,94,0.25);
                "></div>

                <div style="display:flex; flex-direction:column; text-align:left;">
                    <span style="font-weight:600;">
                        {{ session('admin_name', 'Admin user') }}
                    </span>
                    <span style="color:#c4b5fd;">
                        Site administrator — full access
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- 2×2 control-panel grid --}}
    <section style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
        gap:1rem;
        max-width:700px;
        margin:0 auto 2rem;
    ">

        {{-- Operators --}}
        <a href="{{ route('admin.operators') }}"
           style="
               display:flex;
               flex-direction:column;
               justify-content:space-between;
               gap:0.4rem;
               padding:0.9rem 1rem;
               border-radius:0.9rem;
               border:1px solid rgba(148,163,184,0.7);
               background:radial-gradient(circle at top,#020617,#020617 60%,#000 100%);
               color:#e5e7eb;
               text-decoration:none;
               font-size:0.85rem;
           ">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.6rem;">
                <div style="font-size:1.1rem;">🧑‍🚒</div>
                <div style="text-align:right; font-size:0.75rem; color:#9ca3af;">
                    Operator records<br>Roles · Levels · Admin flags
                </div>
            </div>
            <div>
                <div style="font-weight:600; margin-bottom:0.15rem;">Manage operators</div>
                <p style="margin:0; font-size:0.8rem; color:#9ca3af;">
                    Master list of Liverpool RAYNET operators used for the members’ dashboard pill and backend access.
                </p>
            </div>
        </a>

        {{-- Roles --}}
        <a href="{{ route('admin.roles') }}"
           style="
               display:flex;
               flex-direction:column;
               justify-content:space-between;
               gap:0.4rem;
               padding:0.9rem 1rem;
               border-radius:0.9rem;
               border:1px solid rgba(148,163,184,0.7);
               background:#020617;
               color:#e5e7eb;
               text-decoration:none;
               font-size:0.85rem;
           ">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.6rem;">
                <div style="font-size:1.1rem;">🗂️</div>
                <div style="text-align:right; font-size:0.75rem; color:#9ca3af;">
                    Role dictionary<br>Controller · Secretary · Operator…
                </div>
            </div>
            <div>
                <div style="font-weight:600; margin-bottom:0.15rem;">Manage roles</div>
                <p style="margin:0; font-size:0.8rem; color:#9ca3af;">
                    Controlled list that feeds the operator role dropdown and (later) coloured pills.
                </p>
            </div>
        </a>

        {{-- Events --}}
        <a href="{{ route('admin.events') }}"
           style="
               display:flex;
               flex-direction:column;
               justify-content:space-between;
               gap:0.4rem;
               padding:0.9rem 1rem;
               border-radius:0.9rem;
               border:1px solid rgba(148,163,184,0.7);
               background:#020617;
               color:#e5e7eb;
               text-decoration:none;
               font-size:0.85rem;
           ">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.6rem;">
                <div style="font-size:1.1rem;">📅</div>
                <div style="text-align:right; font-size:0.75rem; color:#9ca3af;">
                    Public events<br>Calendar &amp; .ics feeds
                </div>
            </div>
            <div>
                <div style="font-weight:600; margin-bottom:0.15rem;">Manage events</div>
                <p style="margin:0; font-size:0.8rem; color:#9ca3af;">
                    Add, edit and remove events that appear on the public site, calendar view and Members’ hub.
                </p>
            </div>
        </a>

        {{-- Event types --}}
        <a href="{{ route('admin.event-types') }}"
           style="
               display:flex;
               flex-direction:column;
               justify-content:space-between;
               gap:0.4rem;
               padding:0.9rem 1rem;
               border-radius:0.9rem;
               border:1px solid rgba(148,163,184,0.7);
               background:#020617;
               color:#e5e7eb;
               text-decoration:none;
               font-size:0.85rem;
           ">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.6rem;">
                <div style="font-size:1.1rem;">🟦</div>
                <div style="text-align:right; font-size:0.75rem; color:#9ca3af;">
                    Event type colours<br>AGM · Exercise · Public event…
                </div>
            </div>
            <div>
                <div style="font-weight:600; margin-bottom:0.15rem;">Manage event types</div>
                <p style="margin:0; font-size:0.8rem; color:#9ca3af;">
                    Label and colour-code event types; drives the green/blue badges you see across the site.
                </p>
            </div>
        </a>
    </section>

    {{-- Logout, centred under the grid --}}
    <section style="text-align:center; margin-bottom:2.5rem;">
        <form action="{{ route('admin.logout') }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit"
                    style="
                        padding:0.55rem 1.4rem;
                        border-radius:999px;
                        border:1px solid rgba(248,113,113,0.7);
                        background:#1e1b4b;
                        color:#fca5a5;
                        font-size:0.9rem;
                        cursor:pointer;
                    ">
                Log out
            </button>
        </form>
    </section>

@endsection