@extends('layouts.app')

@section('title', 'Event types')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h1>Event types</h1>
            <p style="color:#9ca3af; font-size:0.9rem; margin-top:0.3rem;">
                Use event types to keep your calendar consistent – e.g. Training, Event support, Exercise, Meeting.
                You can also set the colour used for badges on the calendar.
            </p>
        </div>

        <a href="{{ route('admin.events') }}"
           style="padding:0.35rem 0.9rem; border-radius:999px;
                  border:1px solid rgba(148,163,184,0.7); background:#020617;
                  color:#e5e7eb; font-size:0.8rem; text-decoration:none;">
            ← Back to events
        </a>
    </div>

    @if (session('status'))
        <div style="margin-top:0.9rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    border:1px solid rgba(34,197,94,0.7); background:rgba(22,163,74,0.2); color:#bbf7d0;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Add / edit type form --}}
    @php
        /** @var \App\Models\EventType|null $editingType */
        $currentColour = old('colour', $editingType->colour ?? '#22c55e');
    @endphp

    <form method="post"
          action="{{ isset($editingType)
                        ? route('admin.event-types.update', $editingType->id)
                        : route('admin.event-types.store') }}"
          style="margin-top:1.2rem; border-radius:0.75rem;
                 border:1px solid rgba(148,163,184,0.4); padding:1rem; background:#020617;">
        @csrf
        @if (isset($editingType))
            @method('PUT')
        @endif

        <h2 style="font-size:1rem; margin:0 0 0.6rem 0;">
            {{ isset($editingType) ? 'Edit event type' : 'Add event type' }}
        </h2>

        @if (isset($editingType))
            <p style="margin:0 0 0.5rem; font-size:0.8rem; color:#9ca3af;">
                Editing: <strong>{{ $editingType->name }}</strong>.
                <a href="{{ route('admin.event-types') }}" style="color:#93c5fd;">Cancel edit</a>
            </p>
        @endif

        @if ($errors->any())
            <div style="margin-bottom:0.6rem; padding:0.6rem 0.8rem; border-radius:0.5rem;
                        border:1px solid rgba(248,113,113,0.8); background:rgba(127,29,29,0.8); color:#fecaca;">
                {{ $errors->first() }}
            </div>
        @endif

        <div style="display:grid; gap:0.7rem; grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            {{-- Name --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Name *</label>
                <input name="name" type="text"
                       value="{{ old('name', $editingType->name ?? '') }}"
                       placeholder="Training, Event support, Exercise…"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            {{-- Sort order --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Sort order</label>
                <input name="sort_order" type="number"
                       value="{{ old('sort_order', $editingType->sort_order ?? 0) }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#9ca3af;">
                    Lower numbers appear first.
                </p>
            </div>

            {{-- Colour controls --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Badge colour</label>

                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.35rem;">
                    {{-- Native colour picker --}}
                    <input id="colourPicker" type="color"
                           value="{{ $currentColour }}"
                           style="width:40px; height:32px; padding:0; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617;">
                    {{-- Hex text input --}}
                    <input id="colourInput" name="colour" type="text"
                           value="{{ old('colour', $editingType->colour ?? '') }}"
                           placeholder="#22c55e"
                           style="flex:1; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">

                    {{-- Preview chip --}}
                    <div id="colourPreview"
                         style="width:32px; height:24px; border-radius:0.4rem;
                                border:1px solid rgba(148,163,184,0.7);
                                background: {{ $currentColour }};">
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <button type="button"
                            id="resetColourButton"
                            style="padding:0.2rem 0.7rem; border-radius:999px;
                                   border:1px solid rgba(148,163,184,0.7);
                                   background:#020617; color:#e5e7eb; font-size:0.75rem;">
                        Reset to default
                    </button>
                    <p style="margin:0; font-size:0.75rem; color:#9ca3af;">
                        Optional hex colour (e.g. <code>#22c55e</code>). Leave blank for default.
                    </p>
                </div>
            </div>
        </div>

        <button type="submit"
                style="margin-top:0.9rem; padding:0.4rem 1.1rem; border-radius:999px;
                       border:1px solid rgba(56,189,248,0.9); background:#020617; color:#e5e7eb; font-size:0.85rem;">
            {{ isset($editingType) ? 'Update type' : 'Save type' }}
        </button>
    </form>

    {{-- Existing types table --}}
    <div style="margin-top:1.2rem; border-radius:0.75rem; overflow:hidden;
                border:1px solid rgba(148,163,184,0.4); background:#020617;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead style="background:#020617; border-bottom:1px solid rgba(31,41,55,0.9);">
                <tr>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Name</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Slug</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Sort order</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Colour</th>
                    <th style="text-align:right; padding:0.55rem 0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($types as $type)
                    @php
                        $colour = $type->colour ?: '#4b5563';
                    @endphp

                    <tr style="border-top:1px solid rgba(31,41,55,0.9);">
                        <td style="padding:0.55rem 0.75rem; color:#e5e7eb;">
                            {{ $type->name }}
                        </td>

                        <td style="padding:0.55rem 0.75rem; color:#9ca3af;">
                            {{ $type->slug }}
                        </td>

                        <td style="padding:0.55rem 0.75rem; color:#9ca3af;">
                            {{ $type->sort_order }}
                        </td>

                        <td style="padding:0.55rem 0.75rem;">
                            <span style="
                                display:inline-flex;
                                align-items:center;
                                gap:0.4rem;
                                padding:0.1rem 0.45rem;
                                border-radius:999px;
                                border:1px solid {{ $colour }};
                                background: {{ $colour }}1a;
                                font-size:0.78rem;
                                color:#e5e7eb;
                            ">
                                <span style="width:10px; height:10px; border-radius:999px; background:{{ $colour }};"></span>
                                {{ $type->colour ?: 'Default' }}
                            </span>
                        </td>

                        <td style="padding:0.55rem 0.75rem; text-align:right; white-space:nowrap;">
                            <a href="{{ route('admin.event-types', ['edit' => $type->id]) }}"
                               style="color:#93c5fd; text-decoration:none; font-size:0.8rem; margin-right:0.75rem;">
                                Edit
                            </a>

                            <a href="{{ route('admin.event-types.delete', $type->id) }}"
                               onclick="return confirm('Delete this event type?');"
                               style="color:#fca5a5; text-decoration:none; font-size:0.8rem;">
                                Delete
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" style="padding:0.7rem 0.75rem; color:#9ca3af;">
                            No event types yet. Add your first type above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tiny inline JS to wire up the colour picker + preview + reset --}}
    <script>
        (function () {
            const picker  = document.getElementById('colourPicker');
            const input   = document.getElementById('colourInput');
            const preview = document.getElementById('colourPreview');
            const reset   = document.getElementById('resetColourButton');

            if (!picker || !input || !preview || !reset) {
                return;
            }

            const defaultColour = '#22c55e';

            function normaliseHex(value) {
                if (!value) return '';
                value = value.trim();
                if (!value) return '';
                if (value[0] !== '#') value = '#' + value;
                if (!/^#[0-9a-fA-F]{3,6}$/.test(value)) return '';
                // expand #abc -> #aabbcc
                if (value.length === 4) {
                    value = '#' + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
                }
                return value.slice(0, 7).toLowerCase();
            }

            function applyColour(hex) {
                if (!hex) {
                    // fallback to default preview, empty input
                    input.value = '';
                    picker.value = defaultColour;
                    preview.style.background = defaultColour;
                    return;
                }
                input.value = hex;
                picker.value = hex;
                preview.style.background = hex;
            }

            picker.addEventListener('input', function () {
                const hex = normaliseHex(picker.value);
                applyColour(hex || defaultColour);
            });

            input.addEventListener('blur', function () {
                const hex = normaliseHex(input.value);
                if (!hex) {
                    applyColour('');
                } else {
                    applyColour(hex);
                }
            });

            reset.addEventListener('click', function () {
                applyColour('');
            });
        })();
    </script>
@endsection