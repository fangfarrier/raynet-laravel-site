@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:1rem;">
        <div>
            <h1 style="margin:0; color:#e5e7eb;">Upcoming events</h1>
            <p style="margin:0.25rem 0 0; font-size:0.9rem; color:#9ca3af;">
                A simple list of public Liverpool RAYNET events, pulled from the same data as the calendar.
            </p>
        </div>

        <a href="{{ route('calendar') }}"
           style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
            Switch to calendar view →
        </a>
    </div>

    @if ($events->isEmpty())
        <p style="color:#9ca3af; font-size:0.9rem;">
            No upcoming events are currently published.
        </p>
    @else
        <div style="border-radius:0.9rem; border:1px solid rgba(148,163,184,0.5);
                    background:#020617; padding:0.9rem 1rem; font-size:0.9rem;">

            @foreach ($events as $event)
                @php
                    $type = $event->type;
                    $badgeColour = $type && $type->colour ? $type->colour : '#22c55e';
                @endphp

                <article style="padding:0.65rem 0; border-bottom:1px solid rgba(31,41,55,0.9);">
                    <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                        <div style="flex:1 1 260px; min-width:0;">
                            <h2 style="margin:0 0 0.2rem; font-size:1rem;">
                                <a href="{{ $event->url() }}"
                                   style="color:#e5e7eb; text-decoration:none;">
                                    {{ $event->title }}
                                </a>
                            </h2>
                            <div style="font-size:0.85rem; color:#9ca3af; margin-bottom:0.15rem;">
                                {{ $event->displayDate() }}
                                @if ($event->ends_at)
                                    <span> → {{ $event->ends_at->format('D j M Y, H:i') }}</span>
                                @endif
                            </div>
                            @if ($event->location)
                                <div style="font-size:0.85rem; color:#9ca3af; margin-bottom:0.15rem;">
                                    📍 {{ $event->location }}
                                </div>
                            @endif
                            @if ($event->description)
                                <p style="margin:0.15rem 0 0; color:#6b7280; font-size:0.85rem;
                                          max-height:3.5rem; overflow:hidden; text-overflow:ellipsis;">
                                    {{ Str::limit($event->description, 180) }}
                                </p>
                            @endif
                        </div>

                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.4rem;">
                            @if ($type)
                                <span style="
                                    display:inline-flex;
                                    align-items:center;
                                    padding:0.18rem 0.6rem;
                                    border-radius:999px;
                                    border:1px solid {{ $badgeColour }};
                                    background: {{ $badgeColour }}1a;
                                    font-size:0.78rem;
                                    color:#e5e7eb;
                                ">
                                    {{ $type->name }}
                                </span>
                            @endif

                            <a href="{{ route('events.ics', [
                                        'year'  => $event->starts_at->format('Y'),
                                        'month' => $event->starts_at->format('m'),
                                        'slug'  => $event->slug,
                                    ]) }}"
                               style="font-size:0.78rem; color:#93c5fd; text-decoration:none;">
                                Download .ics
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach

            {{-- Pagination --}}
            @if ($events->lastPage() > 1)
                <div style="margin-top:0.5rem; display:flex; justify-content:flex-start; gap:0.35rem; font-size:0.85rem;">
                    @php
                        $current = $events->currentPage();
                        $last    = $events->lastPage();
                    @endphp

                    {{-- Prev --}}
                    @if ($current === 1)
                        <span style="opacity:0.4;">&lt;</span>
                    @else
                        <a href="{{ $events->url($current - 1) }}"
                           style="color:#93c5fd; text-decoration:none;">&lt;</a>
                    @endif

                    {{-- Pages --}}
                    @for ($page = 1; $page <= $last; $page++)
                        @if ($page === $current)
                            <span style="font-weight:600; border-bottom:1px solid #e5e7eb;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $events->url($page) }}"
                               style="color:#a855f7; text-decoration:none;">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next --}}
                    @if ($current === $last)
                        <span style="opacity:0.4;">&gt;</span>
                    @else
                        <a href="{{ $events->url($current + 1) }}"
                           style="color:#93c5fd; text-decoration:none;">&gt;</a>
                    @endif
                </div>
            @endif
        </div>
    @endif
@endsection