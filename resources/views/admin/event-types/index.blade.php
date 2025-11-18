@extends('layouts.app')

@section('title', 'Event types')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h1>Event types</h1>
            <p style="color:#9ca3af; font-size:0.9rem; margin-top:0.3rem;">
                Use event types to keep your calendar consistent – e.g. Training, Event support, Exercise, Meeting.
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

    {{-- Add type form --}}
    <form method="post" action="{{ route('admin.event-types.store') }}"
          style="margin-top:1.2rem; border-radius:0.75rem;
                 border:1px solid rgba(148,163,184,0.4); padding:1rem; background:#020617;">
        @csrf

        <h2 style="font-size:1rem; margin:0 0 0.6rem 0;">Add event type</h2>

        @if ($errors->any())
            <div style="margin-bottom:0.6rem; padding:0.6rem 0.8rem; border-radius:0.5rem;
                        border:1px solid rgba(248,113,113,0.8); background:rgba(127,29,29,0.8); color:#fecaca;">
                {{ $errors->first() }}
            </div>
        @endif

        <div style="display:grid; gap:0.7rem; grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Name *</label>
                <input name="name" type="text"
                       value="{{ old('name') }}"
                       placeholder="Training, Event support, Exercise…"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Sort order</label>
                <input name="sort_order" type="number"
                       value="{{ old('sort_order', 0) }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#9ca3af;">
                    Lower numbers appear first.
                </p>
            </div>
        </div>

        <button type="submit"
                style="margin-top:0.9rem; padding:0.4rem 1.1rem; border-radius:999px;
                       border:1px solid rgba(56,189,248,0.9); background:#020617; color:#e5e7eb; font-size:0.85rem;">
            Save type
        </button>
    </form>

    {{-- Existing types --}}
    <div style="margin-top:1.2rem; border-radius:0.75rem; overflow:hidden;
                border:1px solid rgba(148,163,184,0.4); background:#020617;">
        <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
            <thead style="background:#020617; border-bottom:1px solid rgba(31,41,55,0.9);">
                <tr>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Name</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Slug</th>
                    <th style="text-align:left; padding:0.55rem 0.75rem;">Sort order</th>
                    <th style="text-align:right; padding:0.55rem 0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($types as $type)
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
                        <td style="padding:0.55rem 0.75rem; text-align:right; white-space:nowrap;">
                            <a href="{{ route('admin.event-types.delete', $type->id) }}"
                               onclick="return confirm('Delete this event type?');"
                               style="color:#fca5a5; text-decoration:none; font-size:0.8rem;">
                                Delete
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:0.7rem 0.75rem; color:#9ca3af;">
                            No event types yet. Add your first type above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection