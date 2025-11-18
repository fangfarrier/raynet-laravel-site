@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h1>Manage events</h1>
            <p style="color:#9ca3af; font-size:0.9rem; margin-top:0.3rem;">
                Events here drive both the group calendar and the “Next event” card on the home page.
            </p>
        </div>

        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                    style="padding:0.35rem 0.9rem; border-radius:999px;
                           border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;
                           font-size:0.8rem;">
                Log out
            </button>
        </form>
    </div>

    @if (session('status'))
        <div style="margin-top:0.9rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    border:1px solid rgba(34,197,94,0.7); background:rgba(22,163,74,0.2); color:#bbf7d0;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Quick add form --}}
    <form method="post" action="{{ route('admin.events.store') }}" style="margin-top:1.2rem; border-radius:0.75rem;
                 border:1px solid rgba(148,163,184,0.4); padding:1rem; background:#020617;">
        @csrf

        <h2 style="font-size:1rem; margin:0 0 0.6rem 0;">Add event</h2>

        @if ($errors->any())
            <div style="margin-bottom:0.6rem; padding:0.6rem 0.8rem; border-radius:0.5rem;
                        border:1px solid rgba(248,113,113,0.8); background:rgba(127,29,29,0.8); color:#fecaca;">
                {{ $errors->first() }}
            </div>
        @endif

        <div style="display:grid; gap:0.7rem; grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Title *</label>
                <input name="title" type="text"
                       value="{{ old('title') }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Location</label>
                <input name="location" type="text"
                       value="{{ old('location') }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Start (date &amp; time) *</label>
                <input name="starts_at" type="datetime-local"
                       value="{{ old('starts_at') }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Ends (date &amp; time)</label>
                <input name="ends_at" type="datetime-local"
                       value="{{ old('ends_at') }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Event type *</label>
                <select name="event_type_id"
                        style="width:100%; padding:0.4rem; border-radius:0.4rem;
                               border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                    <option value="">Select type…</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}"
                            {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit"
                style="margin-top:0.9rem; padding:0.4rem 1.1rem; border-radius:999px;
                       border:1px solid rgba(56,189,248,0.9); background:#020617; color:#e5e7eb; font-size:0.85rem;">
            Save event
        </button>
    </form>

    {{-- Existing events + pagination card --}}
    <div style="margin-top:1.2rem; border-radius:0.75rem; overflow:hidden;
                border:1px solid rgba(148,163,184,0.4); background:#020617;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; table-layout:fixed;">

            <colgroup>
                <col style="width:32%;">  {{-- Title --}}
                <col style="width:18%;">  {{-- When --}}
                <col style="width:20%;">  {{-- Location --}}
                <col style="width:14%;">  {{-- Type --}}
                <col style="width:16%;">  {{-- Actions --}}
            </colgroup>

            <thead style="background:#020617; border-bottom:1px solid rgba(31,41,55,0.9);">
                <tr>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Title</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">When</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Location</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Type</th>
                    <th style="text-align:right; padding:0.55rem 0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr style="border-top:1px solid rgba(31,41,55,0.9);">
                        <td style="padding:0.55rem 0.75rem; color:#e5e7eb;">
                            {{ $event->title }}
                        </td>
                        <td style="padding:0.55rem 0.75rem; color:#9ca3af;">
                            {{ method_exists($event, 'displayDate') ? $event->displayDate() : $event->starts_at }}
                        </td>
                        <td style="padding:0.55rem 0.75rem; color:#9ca3af;">
                            {{ $event->location ?? '—' }}
                        </td>
                        <td style="padding:0.55rem 0.75rem; color:#9ca3af;">
                            @php
                                $type        = $event->type;
                                $badgeLabel  = $type?->name ?? '—';
                                $badgeColour = $type?->colour ?: 'rgba(31,41,55,1)';
                            @endphp
                            <span style="
                                display:inline-block;
                                padding:0.1rem 0.6rem;
                                border-radius:999px;
                                background: {{ $badgeColour }};
                                color:#e5e7eb;
                                font-size:0.75rem;
                                white-space:nowrap;
                            ">
                                {{ $badgeLabel }}
                            </span>
                        </td>
                        <td style="padding:0.55rem 0.75rem; text-align:right; white-space:nowrap;">
                            <a href="{{ $event->url() }}"
                               target="_blank"
                               style="color:#93c5fd; text-decoration:none; font-size:0.8rem; margin-right:0.75rem;">
                                View
                            </a>
                            <a href="{{ route('admin.events.delete', $event->id) }}"
                               onclick="return confirm('Delete this event?');"
                               style="color:#fca5a5; text-decoration:none; font-size:0.8rem;">
                                Delete
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:0.7rem 0.75rem; color:#9ca3af;">
                            No events added yet. Use the form above to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($events->lastPage() > 1)
            @php
                $current = $events->currentPage();
                $last    = $events->lastPage();
            @endphp

            <nav aria-label="Event pagination"
                 style="margin-top:0.5rem; padding:0.5rem 0.75rem 0.75rem;
                        display:flex; justify-content:space-between; align-items:center;
                        gap:1rem; font-size:0.85rem; color:#e5e7eb;">

                <div style="display:flex; align-items:center; gap:0.35rem;">
                    @if ($current === 1)
                        <span style="opacity:0.4;">&lt;</span>
                    @else
                        <a href="{{ $events->url($current - 1) }}"
                           rel="prev"
                           style="text-decoration:none; color:#93c5fd;">
                            &lt;
                        </a>
                    @endif

                    @for ($page = 1; $page <= $last; $page++)
                        @if ($page === $current)
                            <span style="font-weight:600; color:#e5e7eb; border-bottom:1px solid #e5e7eb;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $events->url($page) }}"
                               style="text-decoration:none; color:#a855f7;">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($current === $last)
                        <span style="opacity:0.4;">&gt;</span>
                    @else
                        <a href="{{ $events->url($current + 1) }}"
                           rel="next"
                           style="text-decoration:none; color:#93c5fd;">
                            &gt;
                        </a>
                    @endif
                </div>

                <div style="color:#9ca3af;">
                    Showing
                    {{ $events->firstItem() }}
                    to
                    {{ $events->lastItem() }}
                    of
                    {{ $events->total() }}
                    results
                </div>
            </nav>
        @endif
    </div>
@endsection