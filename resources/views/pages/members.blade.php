@extends('layouts.app')

@section('title', 'Members')

@section('content')

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:1.2rem;">
        <div>
            <h1 style="margin:0; color:#e5e7eb;">Member dashboard</h1>
            <p style="margin:0.25rem 0 0; font-size:0.9rem; color:#9ca3af;">
                Quick view of upcoming activity, training and links that matter to Liverpool RAYNET members.
            </p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center;">
            <a href="{{ route('calendar') }}"
               style="padding:0.4rem 0.9rem; border-radius:999px;
                      border:1px solid rgba(148,163,184,0.7); color:#e5e7eb;
                      text-decoration:none; font-size:0.85rem;">
                📅 Full calendar
            </a>
            <a href="{{ route('events.index') }}"
               style="padding:0.4rem 0.9rem; border-radius:999px;
                      border:1px solid rgba(148,163,184,0.7); color:#e5e7eb;
                      text-decoration:none; font-size:0.85rem;">
                📋 Event list
            </a>
        </div>
    </div>

    {{-- Top row: next events + quick links --}}
    <div style="display:grid; grid-template-columns: minmax(0,2.5fr) minmax(0,1.5fr); gap:1rem; margin-bottom:1.5rem;">
        {{-- Upcoming events card --}}
        <section style="
            border-radius:0.9rem;
            border:1px solid rgba(148,163,184,0.5);
            background:#020617;
            padding:0.9rem 1rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:0.6rem; margin-bottom:0.6rem;">
                <div>
                    <h2 style="margin:0; font-size:1rem;">Upcoming events</h2>
                    <p style="margin:0.2rem 0 0; font-size:0.8rem; color:#9ca3af;">
                        Training, exercises and public event support in date order.
                    </p>
                </div>
                <a href="{{ route('calendar') }}"
                   style="font-size:0.78rem; color:#93c5fd; text-decoration:none;">
                    Open calendar →
                </a>
            </div>

            @if ($upcomingEvents->isEmpty())
                <p style="margin:0.3rem 0 0; color:#9ca3af;">
                    No upcoming events scheduled yet. Check back soon or ask the controller about the next exercise.
                </p>
            @else
                <ul style="list-style:none; padding:0; margin:0;">
                    @foreach ($upcomingEvents as $event)
                        @php
                            $type = $event->type;
                            $badgeColour = $type && $type->colour ? $type->colour : '#22c55e';
                        @endphp
                        <li style="padding:0.45rem 0; border-top:1px solid rgba(31,41,55,0.9);">
                            <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:flex-start;">
                                <div style="min-width:0;">
                                    <div style="display:flex; align-items:center; gap:0.4rem; margin-bottom:0.15rem;">
                                        {{-- Type badge --}}
                                        <span style="
                                            display:inline-flex;
                                            align-items:center;
                                            padding:0.12rem 0.45rem;
                                            border-radius:999px;
                                            border:1px solid {{ $badgeColour }};
                                            background: {{ $badgeColour }}1a;
                                            font-size:0.72rem;
                                        ">
                                            {{ $type?->name ?? 'Event' }}
                                        </span>

                                        {{-- Title --}}
                                        <a href="{{ $event->url() }}"
                                           style="color:#e5e7eb; font-weight:500; text-decoration:none; font-size:0.9rem; max-width:18rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $event->title }}
                                        </a>
                                    </div>

                                    <div style="font-size:0.8rem; color:#9ca3af;">
                                        {{ $event->displayDate() }}
                                        @if ($event->ends_at)
                                            <span> → {{ $event->ends_at->format('D j M Y, H:i') }}</span>
                                        @endif
                                        @if ($event->location)
                                            · 📍 {{ $event->location }}
                                        @endif
                                    </div>
                                </div>

                                {{-- ICS button --}}
                                <div style="flex-shrink:0;">
                                    <a href="{{ route('events.ics', [
                                                'year'  => $event->starts_at->format('Y'),
                                                'month' => $event->starts_at->format('m'),
                                                'slug'  => $event->slug,
                                            ]) }}"
                                       style="padding:0.25rem 0.55rem; border-radius:999px;
                                              border:1px solid rgba(148,163,184,0.7);
                                              color:#cbd5f5; text-decoration:none; font-size:0.78rem;">
                                        .ics
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Quick links / status --}}
        <section style="
            border-radius:0.9rem;
            border:1px solid rgba(148,163,184,0.5);
            background:#020617;
            padding:0.9rem 1rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem;">Shortcuts</h2>

            <ul style="list-style:none; padding:0; margin:0;">
                <li style="margin-bottom:0.45rem;">
                    <a href="{{ route('training') }}"
                       style="color:#93c5fd; text-decoration:none; font-size:0.85rem;">
                        📘 Training & exercises
                    </a>
                    <div style="font-size:0.78rem; color:#9ca3af;">
                        Syllabus, modules and planned training sessions.
                    </div>
                </li>

                <li style="margin-bottom:0.45rem;">
                    <a href="{{ route('event-support') }}"
                       style="color:#93c5fd; text-decoration:none; font-size:0.85rem;">
                        🎪 Event support guidance
                    </a>
                    <div style="font-size:0.78rem; color:#9ca3af;">
                        How we support events and what to expect on the day.
                    </div>
                </li>

                <li style="margin-bottom:0.45rem;">
                    <a href="{{ route('request-support') }}"
                       style="color:#93c5fd; text-decoration:none; font-size:0.85rem;">
                        📄 Request RAYNET for an event
                    </a>
                    <div style="font-size:0.78rem; color:#9ca3af;">
                        Share this link with organisers who need comms support.
                    </div>
                </li>

                <li>
                    <span style="color:#e5e7eb; font-size:0.85rem;">
                        📡 Nets & frequencies
                    </span>
                    <div style="font-size:0.78rem; color:#9ca3af;">
                        Once the frequency/status block exists, we’ll surface it here for quick reference.
                    </div>
                </li>
            </ul>
        </section>
    </div>

    {{-- Future expansion placeholder --}}
    <section style="
        border-radius:0.9rem;
        border:1px solid rgba(55,65,81,0.8);
        background:rgba(15,23,42,0.96);
        padding:0.9rem 1rem;
        font-size:0.82rem;
        color:#9ca3af;
    ">
        <p style="margin:0;">
            This dashboard is the framework for member tooling — we can bolt on status boards, document links,
            training progress and more as your backend grows.
        </p>
    </section>

@endsection