@extends('layouts.app')

@section('title', 'Members')

@section('content')
    {{-- Intro --}}
    <section style="margin-bottom:2rem;">
        <h1 style="margin:0 0 0.4rem; color:#e5e7eb;">Members’ hub</h1>
        <p style="margin:0; font-size:0.9rem; color:#9ca3af; max-width:42rem;">
            A single place for Liverpool RAYNET members to find training dates, resources and links to our systems.
        </p>
    </section>

    {{-- Three-column member dashboard --}}
    <section style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1.1rem;">

        {{-- Upcoming events / training --}}
        <article style="
            border-radius:0.9rem;
            border:1px solid rgba(148,163,184,0.5);
            background:rgba(15,23,42,0.96);
            padding:0.9rem 1rem;
            font-size:0.85rem;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem; color:#e5e7eb;">Upcoming events &amp; training</h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Pulled from the public calendar, for quick reference.
            </p>

            @forelse ($upcoming as $event)
                @php
                    $type = $event->type;
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
                            font-size:0.72rem;
                            text-transform:uppercase;
                            letter-spacing:0.08em;
                        ">
                            {{ $type?->name ?? 'Event' }}
                        </span>
                        <span style="font-size:0.78rem; color:#9ca3af;">
                            {{ $event->starts_at?->format('D j M, H:i') }}
                        </span>
                    </div>

                    <div style="font-weight:600; margin-bottom:0.1rem;">
                        <a href="{{ $event->url() }}"
                           style="color:#e5e7eb; text-decoration:none;">
                            {{ $event->title }}
                        </a>
                    </div>

                    @if ($event->location)
                        <div style="font-size:0.8rem; color:#9ca3af;">
                            📍 {{ $event->location }}
                        </div>
                    @endif
                </div>
            @empty
                <p style="margin:0; font-size:0.85rem; color:#9ca3af;">
                    No upcoming events are scheduled yet. Check back soon or look at the full calendar.
                </p>
            @endforelse

            <div style="margin-top:0.6rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="{{ route('calendar') }}"
                   style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
                    View full calendar →
                </a>
                <a href="{{ route('events.index') }}"
                   style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
                    Event list view →
                </a>
            </div>
        </article>

        {{-- Training & progression --}}
        <article style="
            border-radius:0.9rem;
            border:1px solid rgba(148,163,184,0.5);
            background:rgba(15,23,42,0.96);
            padding:0.9rem 1rem;
            font-size:0.85rem;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem; color:#e5e7eb;">Training &amp; progression</h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Quick links to your development as an operator.
            </p>

            <ul style="margin:0 0 0.5rem 1rem; padding:0; color:#e5e7eb; font-size:0.85rem;">
                <li style="margin-bottom:0.25rem;">
                    <a href="{{ route('training') }}" style="color:#93c5fd; text-decoration:none;">
                        Liverpool RAYNET training overview
                    </a>
                </li>
                <li style="margin-bottom:0.25rem;">
                    <a href="{{ route('calendar') }}" style="color:#93c5fd; text-decoration:none;">
                        Training &amp; exercise dates
                    </a>
                </li>
                <li style="margin-bottom:0.25rem;">
                    <a href="https://raynet-training.uk" target="_blank" rel="noopener"
                       style="color:#93c5fd; text-decoration:none;">
                        Online courses &amp; learning environment
                    </a>
                </li>
            </ul>

            <p style="margin:0; font-size:0.78rem; color:#6b7280;">
                As the training system grows, this page will become your single launchpad
                for courses, assessments and resources.
            </p>
        </article>

        {{-- Resources & systems --}}
        <article style="
            border-radius:0.9rem;
            border:1px solid rgba(148,163,184,0.5);
            background:rgba(15,23,42,0.96);
            padding:0.9rem 1rem;
            font-size:0.85rem;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem; color:#e5e7eb;">Resources &amp; systems</h2>
            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Pointers to the wider ecosystem we’ll plug in over time.
            </p>

            <ul style="margin:0 0 0.5rem 1rem; padding:0; color:#e5e7eb; font-size:0.85rem;">
                <li style="margin-bottom:0.25rem;">
                    RAYNET-UK members’ information (national docs, policies, net times)
                </li>
                <li style="margin-bottom:0.25rem;">
                    Local SOPs, checklists and frequency plans
                </li>
                <li style="margin-bottom:0.25rem;">
                    Status board / Ops dashboard (future integration)
                </li>
            </ul>

            <p style="margin:0; font-size:0.78rem; color:#6b7280;">
                For now this is a simple overview card. As more systems come online,
                buttons and login links will appear here.
            </p>
        </article>

    </section>
@endsection