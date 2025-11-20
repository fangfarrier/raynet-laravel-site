@extends('layouts.app')

@section('title', 'Home')

@section('content')

    {{-- HERO SECTION --}}
    <section style="margin-bottom: 2.5rem;">
        <div style="
            border-radius: 1.25rem;
            overflow: hidden;
            position: relative;
            background: radial-gradient(circle at top left, #1d4ed8 0, #020617 55%, #111827 100%);
            border: 1px solid rgba(148,163,184,0.35);
            box-shadow: 0 24px 60px rgba(0,0,0,0.65);
        ">
            {{-- Top banner image --}}
            <div style="border-bottom: 1px solid rgba(15,23,42,0.85); background:#020617;">
                <img
                    src="{{ asset('images/raynet-uk-liverpool-banner.png') }}"
                    alt="Communications by RAYNET-UK Liverpool"
                    style="
                        display:block;
                        max-width:100%;
                        height:auto;
                    "
                >
            </div>

            {{-- Text + STATUS CARD row --}}
            <div style="padding: 1.6rem 1.8rem 1.8rem 1.8rem; display:flex; flex-wrap:wrap; gap:1.75rem; align-items:flex-start;">

                {{-- Status card (left column) --}}
                <div style="flex: 0 0 280px; max-width: 320px;">
                    @include('partials.alert-status-card', ['alertStatus' => $alertStatus ?? null])
                </div>

                {{-- Main hero text (right column) --}}
                <div style="flex: 1 1 260px; min-width: 0;">
                   <p style="
                         text-transform:uppercase;
                         letter-spacing:0.18em;
                         font-size:0.7rem;
                         color:#a5b4fc;
                          margin:0.4rem 0 1.58rem;   /* ⬅ moves it DOWN */
                      ">
                         Zone 10 · Merseyside · Group 179
                       </p>
                    <h1 style="font-size:1.9rem; line-height:1.2; margin:0 0 0.75rem; color:#e5e7eb;">
                        Volunteer emergency communications for Liverpool &amp; Merseyside
                    </h1>
                    <p style="margin:0; color:#cbd5f5; font-size:0.95rem; line-height:1.6;">
                        Liverpool RAYNET supports blue-light services, local authorities and event organisers
                        with resilient radio communications when normal systems are stretched, damaged or simply
                        not available.
                    </p>

                    <div style="margin-top:1.25rem; display:flex; flex-wrap:wrap; gap:0.75rem;">
                        <a href="{{ route('request-support') }}"
                           style="display:inline-flex; align-items:center; gap:0.5rem;
                                  padding:0.55rem 1.2rem; border-radius:999px;
                                  background:linear-gradient(to right,#22d3ee,#0ea5e9);
                                  color:#020617; font-weight:600; font-size:0.9rem; text-decoration:none;">
                            Request RAYNET support
                        </a>
                        <a href="{{ route('event-support') }}"
                           style="display:inline-flex; align-items:center; gap:0.4rem;
                                  padding:0.5rem 1.1rem; border-radius:999px;
                                  border:1px solid rgba(148,163,184,0.7);
                                  color:#e5e7eb; font-size:0.9rem; text-decoration:none;">
                            See how we support events
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- UPCOMING & RECENT ACTIVITY (LIVE DATA) --}}
    <section style="margin-bottom: 2.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:0.9rem;">
            <div>
                <h2 style="margin:0; font-size:1.1rem; color:#e5e7eb;">Upcoming & recent activity</h2>
                <p style="margin:0.25rem 0 0; font-size:0.85rem; color:#9ca3af;">
                    A snapshot of where Liverpool RAYNET has been – and where we’re heading next.
                </p>
            </div>
            <a href="{{ route('calendar') }}"
               style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
                View full calendar →
            </a>
        </div>

        @if ($nextEvent)
            <div style="display:grid; grid-template-columns:minmax(260px,2fr) minmax(220px,1.3fr); gap:1rem; align-items:stretch;">

                {{-- NEXT EVENT – main card --}}
                <article style="
                    border-radius:0.9rem;
                    border:1px solid rgba(148,163,184,0.5);
                    background:radial-gradient(circle at top,#0f172a,#020617 55%);
                    padding:1rem 1.1rem;
                    font-size:0.9rem;
                    color:#e5e7eb;
                ">
                    <p style="margin:0 0 0.35rem; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.12em; color:#a5b4fc;">
                        Next event
                    </p>

                    <h3 style="margin:0 0 0.55rem; font-size:1.05rem;">
                        {{ $nextEvent->title }}
                    </h3>

                    {{-- Type badge --}}
                    @php
                        $type = $nextEvent->type;
                        $colour = $type && $type->colour ? $type->colour : '#38bdf8';
                    @endphp
                    @if ($type)
                        <span style="
                            display:inline-flex;
                            align-items:center;
                            padding:0.15rem 0.6rem;
                            border-radius:999px;
                            border:1px solid {{ $colour }};
                            color:{{ $colour }};
                            font-size:0.75rem;
                            margin-bottom:0.5rem;
                        ">
                            {{ $type->name }}
                        </span>
                    @endif

                    {{-- When / multi-day handling --}}
                    <div style="margin-top:0.35rem; color:#cbd5f5; font-size:0.9rem;">
                        @php
                            $start = $nextEvent->starts_at;
                            $end   = $nextEvent->ends_at;
                        @endphp

                        @if ($end)
                            @if ($start->isSameDay($end))
                                {{ $start->format('D j M Y, H:i') }} – {{ $end->format('H:i') }}
                            @else
                                {{ $start->format('D j M Y, H:i') }}
                                –
                                {{ $end->format('D j M Y, H:i') }}
                            @endif
                        @else
                            {{ $start->format('D j M Y, H:i') }}
                        @endif
                    </div>

                    {{-- Location --}}
                    @if ($nextEvent->location)
                        <p style="margin:0.25rem 0 0; color:#9ca3af; font-size:0.85rem;">
                            Location: <span style="color:#e5e7eb;">{{ $nextEvent->location }}</span>
                        </p>
                    @endif

                    {{-- Actions --}}
                    <div style="margin-top:0.9rem; display:flex; flex-wrap:wrap; gap:0.6rem;">
                        <a href="{{ $nextEvent->url() }}"
                           style="padding:0.4rem 1rem; border-radius:999px;
                                  border:1px solid rgba(56,189,248,0.9);
                                  color:#e5e7eb; text-decoration:none; font-size:0.85rem;">
                            View event details
                        </a>

                        @if ($nextEvent->slug)
                            <a href="{{ route('events.ics', [
                                        'year'  => $nextEvent->starts_at->format('Y'),
                                        'month' => $nextEvent->starts_at->format('m'),
                                        'slug'  => $nextEvent->slug,
                                    ]) }}"
                               style="padding:0.4rem 1rem; border-radius:999px;
                                      border:1px solid rgba(148,163,184,0.7);
                                      color:#cbd5f5; text-decoration:none; font-size:0.85rem;">
                                Add to calendar (.ics)
                            </a>
                        @endif
                    </div>
                </article>

                {{-- OTHER UPCOMING (up to 2) --}}
                <div style="
                    border-radius:0.9rem;
                    border:1px solid rgba(31,41,55,0.9);
                    background:rgba(15,23,42,0.96);
                    padding:0.85rem 0.95rem;
                    font-size:0.85rem;
                ">
                    <p style="margin:0 0 0.4rem; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.12em; color:#9ca3af;">
                        Also coming up
                    </p>

                    @forelse ($otherEvents as $event)
                        <article style="padding:0.45rem 0; border-top:1px solid rgba(31,41,55,0.9);">
                            <p style="margin:0 0 0.1rem; font-size:0.86rem; color:#e5e7eb;">
                                {{ $event->title }}
                            </p>
                            <p style="margin:0; color:#9ca3af;">
                                {{ $event->starts_at->format('D j M Y, H:i') }}
                                @if ($event->location)
                                    · {{ $event->location }}
                                @endif
                            </p>
                        </article>
                    @empty
                        <p style="margin:0; color:#6b7280;">
                            No other events scheduled yet.
                        </p>
                    @endforelse
                </div>
            </div>
        @else
            {{-- No upcoming events – fallback copy --}}
            <article style="
                border-radius:0.9rem;
                border:1px solid rgba(148,163,184,0.5);
                background:rgba(15,23,42,0.96);
                padding:1rem 1.1rem;
                font-size:0.9rem;
                color:#e5e7eb;
            ">
                <h3 style="margin:0 0 0.45rem; font-size:1rem;">No upcoming events scheduled</h3>
                <p style="margin:0 0 0.4rem; color:#9ca3af;">
                    We don’t have any public training or event support booked into the calendar right now.
                </p>
                <p style="margin:0 0 0.7rem; color:#9ca3af;">
                    Controllers may still be running internal nets, exercises or equipment checks behind the scenes.
                </p>
                <a href="{{ route('request-support') }}"
                   style="display:inline-flex; align-items:center; gap:0.4rem;
                          padding:0.45rem 1.1rem; border-radius:999px;
                          border:1px solid rgba(56,189,248,0.9);
                          color:#e5e7eb; text-decoration:none; font-size:0.85rem;">
                    Request RAYNET support for your event
                </a>
            </article>
        @endif
    </section>

    {{-- WHAT WE DO --}}
    <section style="margin-bottom: 2.5rem;">
        <h2 style="margin:0 0 0.75rem; font-size:1.1rem; color:#e5e7eb;">What we do</h2>
        <p style="margin:0 0 1.1rem; font-size:0.9rem; color:#9ca3af; max-width:40rem;">
            Liverpool RAYNET provides radio communications to support the emergency services, local
            authorities, and voluntary sector partners – as well as safety cover for public events.
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1.1rem;">

            <div style="
                border-radius:0.85rem;
                border:1px solid rgba(148,163,184,0.5);
                background:rgba(15,23,42,0.96);
                padding:0.9rem 1rem;
                font-size:0.88rem;
                color:#e5e7eb;
            ">
                <p style="margin:0 0 0.4rem; font-size:1.5rem;">🚑</p>
                <h3 style="margin:0 0 0.35rem; font-size:0.98rem;">Support to responders</h3>
                <p style="margin:0; color:#9ca3af;">
                    We provide extra radio capacity and link paths when normal systems are overloaded,
                    unreliable or out of coverage.
                </p>
            </div>

            <div style="
                border-radius:0.85rem;
                border:1px solid rgba(148,163,184,0.5);
                background:rgba(15,23,42,0.96);
                padding:0.9rem 1rem;
                font-size:0.88rem;
                color:#e5e7eb;
            ">
                <p style="margin:0 0 0.4rem; font-size:1.5rem;">🎪</p>
                <h3 style="margin:0 0 0.35rem; font-size:0.98rem;">Event communications</h3>
                <p style="margin:0; color:#9ca3af;">
                    From charity runs to large festivals, we build radio nets that keep organisers, marshals
                    and medical teams talking.
                </p>
            </div>

            <div style="
                border-radius:0.85rem;
                border:1px solid rgba(148,163,184,0.5);
                background:rgba(15,23,42,0.96);
                padding:0.9rem 1rem;
                font-size:0.88rem;
                color:#e5e7eb;
            ">
                <p style="margin:0 0 0.4rem; font-size:1.5rem;">📻</p>
                <h3 style="margin:0 0 0.35rem; font-size:0.98rem;">Training &amp; resilience</h3>
                <p style="margin:0; color:#9ca3af;">
                    Our volunteers train regularly in radio procedure, mapping, power, and JESIP-aligned
                    working so we’re useful on day one.
                </p>
            </div>

            <div style="
                border-radius:0.85rem;
                border:1px solid rgba(148,163,184,0.5);
                background:rgba(15,23,42,0.96);
                padding:0.9rem 1rem;
                font-size:0.88rem;
                color:#e5e7eb;
            ">
                <p style="margin:0 0 0.4rem; font-size:1.5rem;">🤝</p>
                <h3 style="margin:0 0 0.35rem; font-size:0.98rem;">Working with partners</h3>
                <p style="margin:0; color:#9ca3af;">
                    We’re part of the wider RAYNET-UK structure and work alongside local resilience forums
                    and voluntary sector partners.
                </p>
            </div>

        </div>
    </section>

@endsection