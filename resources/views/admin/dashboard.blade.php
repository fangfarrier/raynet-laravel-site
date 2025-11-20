@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')

    @php
        /** @var \App\Models\AlertStatus|null $alertStatus */
        $alertStatus   = \App\Models\AlertStatus::query()->first();
        $alertMeta     = $alertStatus?->meta();
        $currentLevel  = $alertStatus->level ?? 5;
        $currentColour = $alertMeta['colour'] ?? '#22c55e';

        // Text colour tweak for readability (esp. Level 3 yellow)
        $textColour = '#020617';
        if (in_array($currentLevel, [1, 2, 4], true)) {
            $textColour = '#0b1120';
        }
    @endphp

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
                    Central control panel for events, operators, roles and global alert status.
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

    {{-- ROW: Alert preview (left) + 4 admin cards (right grid) --}}
    <section style="
        display:flex;
        flex-wrap:wrap;
        gap:1.2rem;
        align-items:flex-start;
        margin-bottom:1.8rem;
    ">

        {{-- ALERT PREVIEW CARD (LEFT) --}}
        <div style="
            max-width: 360px;
            flex: 0 0 320px;
            border-radius: 1.2rem;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15,23,42,0.85);
            border: 1px solid rgba(15,23,42,0.95);
            background: #020617;
            font-size: 0.9rem;
        ">
            {{-- Header strip --}}
            <div style="
                display:flex;
                background:#020617;
                border-bottom:1px solid rgba(15,23,42,0.95);
                height:46px;
            ">
                <div style="
                    flex:1;
                    padding:0.35rem 0.9rem;
                    background:#0f172a;
                    color:#e5e7eb;
                    font-size:0.78rem;
                    text-transform:uppercase;
                    letter-spacing:0.12em;
                    font-weight:600;
                    border-right:1px solid rgba(15,23,42,0.95);
                    display:flex;
                    align-items:center;
                ">
                    Liverpool RAYNET
                </div>

                {{-- Big centred level digit --}}
                <div style="
                    width:60px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    background:#020617;
                    color:#f9fafb;
                    font-size:1.5rem;
                    font-weight:800;
                ">
                    {{ $currentLevel }}
                </div>

                <div style="
                    flex:1;
                    padding:0.35rem 0.9rem;
                    background:#020617;
                    color:#cbd5f5;
                    font-size:0.78rem;
                    text-transform:uppercase;
                    letter-spacing:0.12em;
                    font-weight:600;
                    text-align:right;
                    display:flex;
                    align-items:center;
                    justify-content:flex-end;
                ">
                    RAYNET Status
                </div>
            </div>

            {{-- Body mirrors public card --}}
            <div style="
                padding:1.1rem 1.1rem 1.1rem;
                background: {{ $currentColour }};
                color: {{ $textColour }};
            ">
                <div style="font-size:1.1rem; font-weight:800; margin-bottom:0.2rem;">
                    Level {{ $currentLevel }}
                </div>

                <div style="font-weight:700; margin-bottom:0.45rem;">
                    {{ $alertMeta['title'] ?? 'Alert Level '.$currentLevel }}
                </div>

                <div style="font-size:0.9rem; line-height:1.5; margin-bottom:0.7rem;">
                    {{ $alertMeta['description'] ?? '' }}
                </div>

                @if(!empty($alertStatus?->message))
                    <div style="
                        margin-bottom:0.6rem;
                        padding:0.55rem 0.8rem;
                        border-radius:0.8rem;
                        border:1px dashed rgba(15,23,42,0.4);
                        background: rgba(255,255,255,0.25);
                        font-size:0.9rem;
                        font-weight:600;
                    ">
                        {{ $alertStatus->message }}
                    </div>
                @endif

                <div style="
                    margin-top:0.2rem;
                    padding:0.45rem 0.75rem;
                    border-radius:999px;
                    background: rgba(255,255,255,0.35);
                    font-size:0.78rem;
                    font-weight:500;
                ">
                    This is exactly what the public and members see.
                </div>
            </div>
        </div>

        {{-- RIGHT: 2×2 GRID OF ADMIN CARDS --}}
        <div style="
            flex:1 1 260px;
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:1rem;
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

        </div>
    </section>

    {{-- FULL-WIDTH: Update global alert level --}}
    <section style="
        margin-top:0.5rem;
        border-radius:0.75rem;
        border:1px solid rgba(148,163,184,0.4);
        padding:1rem;
        background:#020617;
    ">
        <h2 style="margin:0 0 0.6rem; font-size:1rem;">Update global alert level</h2>
        <p style="margin:0 0 0.8rem; font-size:0.85rem; color:#9ca3af;">
            This form drives the public alert banner, footer strip and the status tiles on the homepage and members’ hub.
            Levels <strong>1–3</strong> display a visible banner under the main navigation.
        </p>

        <form method="post" action="{{ route('admin.alert-status.update') }}"
              style="display:grid; gap:0.7rem;
                     grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
                     align-items:flex-start;">
            @csrf

            {{-- Level selector --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Alert level</label>
                <select name="level"
                        style="width:100%; padding:0.45rem; border-radius:0.4rem;
                               border:1px solid rgba(148,163,184,0.7);
                               background:#020617; color:#e5e7eb;">
                    @foreach (\App\Models\AlertStatus::config() as $level => $meta)
                        <option value="{{ $level }}" {{ $currentLevel == $level ? 'selected' : '' }}>
                            {{ $meta['title'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Headline --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Headline (optional)</label>
                <input type="text" name="headline"
                       value="{{ old('headline', optional($alertStatus)->headline) }}"
                       placeholder="e.g. Storm Babet – stand by for possible activation"
                       style="width:100%; padding:0.45rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7);
                              background:#020617; color:#e5e7eb;">
            </div>

            {{-- Message --}}
            <div style="grid-column:1 / -1;">
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Message (optional)</label>
                <textarea name="message" rows="3"
                          placeholder="If left blank, the default description for the chosen level will be shown."
                          style="width:100%; padding:0.45rem; border-radius:0.4rem;
                                 border:1px solid rgba(148,163,184,0.7);
                                 background:#020617; color:#e5e7eb; resize:vertical;">{{ old('message', optional($alertStatus)->message) }}</textarea>
            </div>

            <div style="grid-column:1 / -1; display:flex; justify-content:flex-end; margin-top:0.3rem;">
                <button type="submit"
                        style="padding:0.45rem 0.9rem; border-radius:999px;
                               border:none; background:#22c55e; color:#020617;
                               font-size:0.85rem; font-weight:600; cursor:pointer;">
                    Update alert level
                </button>
            </div>
        </form>

        @if ($alertStatus)
            <p style="margin:0.6rem 0 0; font-size:0.75rem; color:#6b7280;">
                Current: <strong>{{ $alertMeta['title'] ?? 'Unknown level' }}</strong>
                @if (!empty($alertStatus->headline))
                    — {{ $alertStatus->headline }}
                @endif
            </p>
        @else
            <p style="margin:0.6rem 0 0; font-size:0.75rem; color:#6b7280;">
                No alert record yet. Once you save a level, it will drive the public banner, footer and member tiles.
            </p>
        @endif
    </section>

    {{-- Logout, centred under everything --}}
    <section style="text-align:center; margin-top:2rem; margin-bottom:2.5rem;">
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