@extends('layouts.app')

@section('title', 'Members')

@section('content')

    {{-- Quick status strip so I can see who I am at a glance --}}
    @php
        $roleColour = $operator['role_colour'] ?? null;
    @endphp

    <section style="margin-bottom: 1.4rem;">
        <div style="
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:flex-start;
            gap:0.75rem;
        ">
            <div>
                <h1 style="margin:0 0 0.35rem; font-size:1.2rem; color:#e5e7eb;">
                    Members’ hub
                </h1>
                <p style="margin:0; font-size:0.9rem; color:#9ca3af; max-width:40rem;">
                    My single jumping-off point for Liverpool RAYNET training dates, resources and back-end systems.
                </p>
            </div>

            <div style="
                display:flex;
                flex-wrap:wrap;
                gap:0.5rem;
                align-items:center;
            ">
                {{-- Member role pill is already in the global chrome – this one focuses on my operator status --}}
                <div style="
                    min-width: 230px;
                    border-radius: 999px;
                    padding: 0.45rem 0.9rem;
                    background: radial-gradient(circle at top left,#1d4ed8,#020617);
                    border: 1px solid rgba(59,130,246,0.9);
                    font-size: 0.78rem;
                    color:#e5e7eb;
                    display:flex;
                    align-items:center;
                    gap:0.5rem;
                ">
                    {{-- Active dot --}}
                    <div style="
                        width: 9px;
                        height: 9px;
                        border-radius:999px;
                        background:#22c55e;
                        box-shadow:0 0 0 4px rgba(34,197,94,0.25);
                    "></div>

                    {{-- Text + optional role badge --}}
                    <div style="display:flex; flex-direction:column;">
                        {{-- Name + callsign on top line --}}
                        <span style="font-weight:600;">
                            {{ $operator['name'] ?? 'Operator' }}
                            @if (!empty($operator['callsign']))
                                · {{ $operator['callsign'] }}
                            @endif
                        </span>

                        {{-- Role + Level underneath --}}
                        <span style="color:#a5b4fc;">
                            {{ $operator['role'] ?? 'Member' }}
                            @if (!empty($operator['level']))
                                — Level: {{ $operator['level'] }}
                            @endif
                        </span>

                        {{-- Tiny role badge tinted by role colour, if defined --}}
                        @if ($roleColour)
                            <span style="
                                margin-top:0.18rem;
                                align-self:flex-start;
                                padding:0.08rem 0.45rem;
                                border-radius:999px;
                                border:1px solid {{ $roleColour }};
                                background: {{ $roleColour }}1a;
                                font-size:0.7rem;
                                letter-spacing:0.08em;
                                text-transform:uppercase;
                            ">
                                {{ $operator['role'] ?? 'Role' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main dashboard grid --}}
    <section style="display:grid; grid-template-columns:2fr 2fr; gap:1.2rem; align-items:flex-start; margin-bottom:1.4rem;">
        {{-- Upcoming events & training (left column) --}}
        <article style="
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background: radial-gradient(circle at top left,#020617,#020617 55%,#020617 100%);
            padding:0.9rem 1rem 0.8rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem; color:#e5e7eb;">
                Upcoming events &amp; training
            </h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Pulled from the public calendar so I can see what’s coming up without leaving the members’ area.
            </p>

            @forelse ($upcoming as $event)
                @php
                    $type   = $event->type;
                    $colour = $type && $type->colour ? $type->colour : '#22c55e';
                @endphp

                <div style="margin-bottom:0.6rem; padding-bottom:0.5rem; border-bottom:1px dashed rgba(31,41,55,0.9);">
                    <div style="display:flex; align-items:center; gap:0.4rem; margin-bottom:0.15rem;">
                        <span style="
                            display:inline-flex;
                            align-items:center;
                            padding:0.1rem 0.45rem;
                            border-radius:999px;
                            border:1px solid {{ $colour }};
                            background: {{ $colour }}1a;
                            font-size:0.7rem;
                            letter-spacing:0.09em;
                            text-transform:uppercase;
                        ">
                            {{ $type?->name ?? 'Event' }}
                        </span>
                        <span style="color:#9ca3af; font-size:0.78rem;">
                            {{ $event->displayDate() }}
                        </span>
                    </div>

                    <div style="font-weight:600; margin-bottom:0.1rem;">
                        <a href="{{ $event->url() }}" style="color:#e5e7eb; text-decoration:none;">
                            {{ $event->title }}
                        </a>
                    </div>

                    @if ($event->location)
                        <div style="color:#9ca3af; font-size:0.8rem; margin-bottom:0.1rem;">
                            📍 {{ $event->location }}
                        </div>
                    @endif

                    @if ($event->description)
                        <div style="color:#6b7280; font-size:0.8rem;">
                            {{ \Illuminate\Support\Str::limit($event->description, 140) }}
                        </div>
                    @endif
                </div>
            @empty
                <p style="margin:0; font-size:0.8rem; color:#9ca3af;">
                    No upcoming events found yet. The moment something is added to the calendar it will appear here.
                </p>
            @endforelse

            <div style="display:flex; gap:0.75rem; margin-top:0.2rem; font-size:0.8rem;">
                <a href="{{ route('calendar') }}"
                   style="color:#93c5fd; text-decoration:none;">
                    View full calendar →
                </a>
                <a href="{{ route('events.index') }}"
                   style="color:#93c5fd; text-decoration:none;">
                    Event list view →
                </a>
            </div>
        </article>

        {{-- Training & progression (right column) --}}
        <article style="
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background: rgba(15,23,42,0.96);
            padding:0.9rem 1rem 0.8rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem; color:#e5e7eb;">
                Training &amp; progression
            </h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                This is my personal launchpad into the training ecosystem – pathways, modules and sign-off.
            </p>

            <ul style="margin:0 0 0.5rem 1rem; padding:0; font-size:0.83rem;">
                @foreach ($trainingLinks as $link)
                    <li style="margin-bottom:0.25rem;">
                        <a href="{{ $link['url'] }}"
                           style="color:#bfdbfe; text-decoration:none;">
                            {{ $link['label'] }} →
                        </a>
                    </li>
                @endforeach
            </ul>

            <p style="margin:0; font-size:0.78rem; color:#6b7280;">
                As the Moodle and self-hosted systems grow, this panel will become a proper “My learning”
                dashboard driven from the back end.
            </p>
        </article>
    </section>

    {{-- Second row – resources and back-end systems --}}
    <section style="display:grid; grid-template-columns:2fr 2fr; gap:1.2rem;">
        {{-- Resources & systems --}}
        <article style="
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background: rgba(15,23,42,0.96);
            padding:0.9rem 1rem 0.9rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem;">Resources &amp; systems</h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Pointers into the wider ecosystem I rely on during planning, training and live ops.
            </p>

            <ul style="margin:0 0 0.6rem 1rem; padding:0; font-size:0.83rem;">
                @foreach ($resources as $resource)
                    <li style="margin-bottom:0.25rem;">
                        <a href="{{ $resource['url'] }}"
                           style="color:#bfdbfe; text-decoration:none;">
                            {{ $resource['label'] }} →
                        </a>
                    </li>
                @endforeach
            </ul>

            <p style="margin:0; font-size:0.78rem; color:#6b7280;">
                Over time I can swap these static links for real integrations – shared drives, document systems
                and live dashboards.
            </p>
        </article>

        {{-- Back-end & Ops Board hooks --}}
        <article style="
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background: radial-gradient(circle at top,#020617,#020617 60%,#020617 100%);
            padding:0.9rem 1rem 0.9rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem;">
                Back-end &amp; Ops Board
            </h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                These links will eventually drop me straight into the self-hosted operational stack.
            </p>

            <div style="display:flex; flex-direction:column; gap:0.4rem; margin-bottom:0.6rem;">
                @if (!empty($opsSystems['ops_board_url']))
                    <a href="{{ $opsSystems['ops_board_url'] }}"
                       style="
                           display:inline-flex;
                           align-items:center;
                           justify-content:space-between;
                           padding:0.45rem 0.75rem;
                           border-radius:0.7rem;
                           border:1px solid rgba(96,165,250,0.8);
                           background:rgba(15,23,42,0.96);
                           text-decoration:none;
                           color:#dbeafe;
                           font-size:0.85rem;
                       ">
                        <span>Status board / Ops dashboard</span>
                        <span style="font-size:0.8rem; color:#93c5fd;">Open ↗</span>
                    </a>
                @endif

                @if (!empty($opsSystems['backend_url']))
                    <a href="{{ $opsSystems['backend_url'] }}"
                       style="
                           display:inline-flex;
                           align-items:center;
                           justify-content:space-between;
                           padding:0.45rem 0.75rem;
                           border-radius:0.7rem;
                           border:1px solid rgba(52,211,153,0.8);
                           background:rgba(6,95,70,0.35);
                           text-decoration:none;
                           color:#bbf7d0;
                           font-size:0.85rem;
                       ">
                        <span>Self-hosted back-end (admin tools)</span>
                        <span style="font-size:0.8rem; color:#6ee7b7;">Open ↗</span>
                    </a>
                @endif
            </div>

            <p style="margin:0; font-size:0.78rem; color:#6b7280;">
                For now these are just bookmarks. Once the back-end is live, this tile becomes the bridge between
                the public web and the operational tools.
            </p>
        </article>
    </section>

@endsection