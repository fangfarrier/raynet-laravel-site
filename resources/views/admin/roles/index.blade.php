@extends('layouts.app')

@section('title', 'Roles')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h1>Roles</h1>
            <p style="color:#9ca3af; font-size:0.9rem; margin-top:0.3rem;">
                This is my controlled list of roles – e.g. Group Controller, Secretary, Treasurer, Operator.
                Operators pick from this list so everything stays consistent.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           style="padding:0.35rem 0.9rem; border-radius:999px;
                  border:1px solid rgba(148,163,184,0.7); background:#020617;
                  color:#e5e7eb; font-size:0.8rem; text-decoration:none;">
            ← Back to admin dashboard
        </a>
    </div>

    @if (session('status'))
        <div style="margin-top:0.9rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    border:1px solid rgba(34,197,94,0.7); background:rgba(22,163,74,0.2); color:#bbf7d0;">
            {{ session('status') }}
        </div>
    @endif

    @php
        /** @var \App\Models\Role|null $editingRole */
    @endphp

    {{-- Add / edit role form --}}
    <form method="post"
          action="{{ isset($editingRole)
                        ? route('admin.roles.update', $editingRole->id)
                        : route('admin.roles.store') }}"
          style="margin-top:1.2rem; border-radius:0.75rem;
                 border:1px solid rgba(148,163,184,0.4); padding:1rem; background:#020617;">
        @csrf

        <h2 style="font-size:1rem; margin:0 0 0.6rem 0;">
            {{ isset($editingRole) ? 'Edit role' : 'Add role' }}
        </h2>

        @if (isset($editingRole))
            <p style="margin:0 0 0.5rem; font-size:0.8rem; color:#9ca3af;">
                Editing: <strong>{{ $editingRole->name }}</strong>.
                <a href="{{ route('admin.roles') }}" style="color:#93c5fd;">Cancel edit</a>
            </p>
        @endif

        @if ($errors->any())
            <div style="margin-bottom:0.6rem; padding:0.6rem 0.8rem; border-radius:0.5rem;
                        border:1px solid rgba(248,113,113,0.8); background:rgba(127,29,29,0.8); color:#fecaca;">
                {{ $errors->first() }}
            </div>
        @endif

        <div style="display:grid; gap:0.7rem; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
            {{-- Name --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Role name *</label>
                <input name="name" type="text"
                       value="{{ old('name', $editingRole->name ?? '') }}"
                       placeholder="Group Controller, Secretary, Operator…"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            {{-- Sort order --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Sort order</label>
                <input name="sort_order" type="number"
                       value="{{ old('sort_order', $editingRole->sort_order ?? 0) }}"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#6b7280;">
                    Lower numbers appear earlier in dropdowns.
                </p>
            </div>

            {{-- Badge colour --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Badge colour</label>
                <input name="colour" type="text"
                       value="{{ old('colour', $editingRole->colour ?? '') }}"
                       placeholder="#22c55e"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#6b7280;">
                    Optional hex colour (e.g. <code>#22c55e</code>). Leave blank to use the default pill styling.
                </p>
            </div>
        </div>

        <div style="margin-top:0.9rem;">
            <button type="submit"
                    style="padding:0.45rem 0.9rem; border-radius:999px;
                           border:none; background:#22c55e; color:#020617;
                           font-size:0.85rem; font-weight:600; cursor:pointer;">
                {{ isset($editingRole) ? 'Update role' : 'Save role' }}
            </button>
        </div>
    </form>

    {{-- Existing roles table --}}
    <section style="margin-top:1.3rem;">
        <h2 style="font-size:0.95rem; margin:0 0 0.5rem;">Role list</h2>

        @if ($roles->isEmpty())
            <p style="font-size:0.8rem; color:#9ca3af;">
                No roles yet. Once I add records above, they’ll show here and become available in the operators screen.
            </p>
        @else
            <div style="
                border-radius:0.8rem;
                border:1px solid rgba(148,163,184,0.5);
                background:#020617;
                overflow:hidden;
                font-size:0.8rem;
            ">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                    <tr style="background:#020617; color:#9ca3af; text-align:left;">
                        <th style="padding:0.55rem 0.75rem;">Name</th>
                        <th style="padding:0.55rem 0.75rem;">Sort order</th>
                        <th style="padding:0.55rem 0.75rem;">Colour</th>
                        <th style="padding:0.55rem 0.75rem; text-align:right;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($roles as $role)
                        @php
                            $colour = $role->colour ?: '#4b5563';
                        @endphp
                        <tr style="border-top:1px solid rgba(31,41,55,0.95);">
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $role->name }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $role->sort_order }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                @if ($role->colour)
                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:0.4rem;
                                        font-size:0.78rem;
                                    ">
                                        <span style="
                                            width:14px;
                                            height:14px;
                                            border-radius:999px;
                                            background: {{ $colour }};
                                            border:1px solid rgba(15,23,42,0.9);
                                            box-shadow:0 0 0 2px rgba(15,23,42,0.9);
                                        "></span>
                                        <span>{{ $role->colour }}</span>
                                    </span>
                                @else
                                    <span style="font-size:0.78rem; color:#6b7280;">
                                        Default
                                    </span>
                                @endif
                            </td>
                            <td style="padding:0.5rem 0.75rem; text-align:right;">
                                <a href="{{ route('admin.roles', ['edit' => $role->id]) }}"
                                   style="margin-right:0.5rem; color:#93c5fd; text-decoration:none;">
                                    Edit
                                </a>
                                <a href="{{ route('admin.roles.delete', $role->id) }}"
                                   style="color:#fca5a5; text-decoration:none;"
                                   onclick="return confirm('Delete this role? It will not change existing operator records, but will remove it from the dropdown.');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection