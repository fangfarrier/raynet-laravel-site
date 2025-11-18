@extends('layouts.app')

@section('title', $event->title)

@section('content')
    {{-- Breadcrumb / back link --}}
    <p style="font-size:0.8rem; color:#9ca3af; margin-bottom:0.6rem;">
        <a href="{{ route('calendar') }}" style="color:#93c5fd; text-decoration:none;">
            ← Back to calendar
        </a>
    </p>

    {{-- Title --}}
    <h1>{{ $event->title }}</h1>

    {{-- Meta strip: date, location, type --}}
    <div style="margin-top:0.8rem; display:grid; gap:0.8rem;
                grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
        {{-- When --}}
        <div>
            <div style="font-size:0.8rem; color:#9ca3af;">When</div>
            <div style="font-size:1rem; color:#e5e7eb;">
                {{ $event->displayDate() }}

                @if ($event->ends_at)
                    <div style="font-size:0.85rem; color:#9ca3af; margin-top:0.15rem;">
                        Ends: {{ $event->ends_at->format('D j M Y, H:i') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Location --}}
        @if ($event->location)
            <div>
                <div style="font-size:0.8rem; color:#9ca3af;">Location</div>
                <div style="font-size:1rem; color:#e5e7eb;">
                    {{ $event->location }}
                </div>
            </div>
        @endif

        {{-- Type --}}
        @if ($event->type || $event->category)
            <div>
                <div style="font-size:0.8rem; color:#9ca3af;">Type</div>
                <div style="margin-top:0.2rem;">
                    @if ($event->type)
                        @php
                            $badgeColour = $event->type->colour ?: '#22c55e';
                        @endphp
                        <span style="
                            display:inline-flex;
                            align-items:center;
                            padding:0.18rem 0.6rem;
                            border-radius:999px;
                            border:1px solid {{ $badgeColour }};
                            background: {{ $badgeColour }}1a;
                            font-size:0.82rem;
                            color:#e5e7eb;
                        ">
                            {{ $event->type->name }}
                        </span>
                    @elseif($event->category)
                        <span style="font-size:0.9rem; color:#e5e7eb;">
                            {{ $event->category }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Description --}}
    @if ($event->description)
        <div style="margin-top:1.2rem; max-width:44rem;">
            <div style="font-size:0.8rem; color:#9ca3af; margin-bottom:0.3rem;">Details</div>
            <p style="color:#e5e7eb; line-height:1.5; white-space:pre-line;">
                {{ $event->description }}
            </p>
        </div>
    @endif

    {{-- Actions --}}
    <div style="margin-top:1.4rem; display:flex; flex-wrap:wrap; gap:0.7rem; align-items:center;">

        {{-- Add to calendar --}}
        <a href="{{ route('events.ics', [
                    'year'  => $event->starts_at->format('Y'),
                    'month' => $event->starts_at->format('m'),
                    'slug'  => $event->slug,
                ]) }}"
           style="padding:0.45rem 1.1rem; border-radius:999px;
                  border:1px solid rgba(148,163,184,0.7);
                  color:#cbd5f5; text-decoration:none; font-size:0.85rem;">
            Add to calendar (.ics)
        </a>

        {{-- Request future support --}}
        <a href="{{ route('request-support') }}"
           style="padding:0.45rem 1.1rem; border-radius:999px;
                  border:1px solid rgba(56,189,248,0.9); background:#020617;
                  color:#e5e7eb; text-decoration:none; font-size:0.85rem;">
            Request RAYNET support for a future event
        </a>

        @if (session('is_admin') === true)
            <span style="font-size:0.8rem; color:#9ca3af;">
                (Admin: manage via
                <a href="{{ route('admin.events') }}" style="color:#93c5fd;">Events admin</a>.)
            </span>
        @endif
    </div>
@endsection