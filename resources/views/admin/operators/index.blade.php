@extends('layouts.app')

@section('title', 'Manage operators')

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h1>Manage operators</h1>
            <p style="color:#9ca3af; font-size:0.9rem; margin-top:0.3rem;">
                This is my master list of Liverpool RAYNET operators – callsigns, roles, levels, statuses and admin flags.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           style="padding:0.35rem 0.9rem; border-radius:999px;
                  border:1px solid rgba(148,163,184,0.7); background:#020617;
                  color:#e5e7eb; font-size:0.8rem; text-decoration:none;">
            ← Back to admin
        </a>
    </div>

    @if (session('status'))
        <div style="margin-top:0.9rem; padding:0.75rem 1rem; border-radius:0.5rem;
                    border:1px solid rgba(34,197,94,0.7); background:rgba(22,163,74,0.2); color:#bbf7d0;">
            {{ session('status') }}
        </div>
    @endif

    @php
        /** @var \App\Models\Operator|null $editingOperator */
        /** @var \Illuminate\Support\Collection|\App\Models\Role[] $roles */
        $statuses        = ['Active', 'Training', 'On hold', 'Inactive'];
        $currentRole     = old('role', $editingOperator->role ?? '');
        $hasRolesDefined = $roles->isNotEmpty();
        // Map role name → colour so I can tint the table cells
        $roleColours     = $roles->pluck('colour', 'name');
    @endphp

    {{-- Add / edit operator form --}}
    <form method="post"
          action="{{ isset($editingOperator)
                        ? route('admin.operators.update', $editingOperator->id)
                        : route('admin.operators.store') }}"
          style="margin-top:1.2rem; border-radius:0.75rem;
                 border:1px solid rgba(148,163,184,0.4); padding:1rem; background:#020617;">
        @csrf
        @if (isset($editingOperator))
            @method('PUT')
        @endif

        <h2 style="font-size:1rem; margin:0 0 0.6rem 0;">
            {{ isset($editingOperator) ? 'Edit operator' : 'Add operator' }}
        </h2>

        @if (isset($editingOperator))
            <p style="margin:0 0 0.5rem; font-size:0.8rem; color:#9ca3af;">
                Editing: <strong>{{ $editingOperator->name }}</strong> ({{ $editingOperator->callsign }}).
                <a href="{{ route('admin.operators') }}" style="color:#93c5fd;">Cancel edit</a>
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
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Name *</label>
                <input name="name" type="text"
                       value="{{ old('name', $editingOperator->name ?? '') }}"
                       placeholder="Ian Jones"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            {{-- Callsign --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Callsign</label>
                <input name="callsign" type="text"
                       value="{{ old('callsign', $editingOperator->callsign ?? '') }}"
                       placeholder="G4BDS"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>
            {{-- Email --}}
<div>
    <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Email *</label>
    <input name="email" type="email"
           value="{{ old('email', $editingOperator->email ?? '') }}"
           placeholder="operator@example.com"
           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
</div>
            {{-- Role (dropdown from roles admin) --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Role</label>

                @if ($hasRolesDefined)
                    <select name="role"
                            style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                   border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                        <option value="">— Select role —</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ $currentRole === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#6b7280;">
                        Need to change the list? Use the
                        <a href="{{ route('admin.roles') }}" style="color:#93c5fd;">Roles screen</a>.
                    </p>
                @else
                    <input name="role" type="text"
                           value="{{ $currentRole }}"
                           placeholder="Group Controller, Operator…"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                    <p style="margin:0.25rem 0 0; font-size:0.75rem; color:#f97316;">
                        No roles defined yet. I can create them under
                        <a href="{{ route('admin.roles') }}" style="color:#fdba74;">Admin → Roles</a>.
                    </p>
                @endif
            </div>

            {{-- Level --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Level</label>
                <input name="level" type="text"
                       value="{{ old('level', $editingOperator->level ?? '') }}"
                       placeholder="Level 0–5"
                       style="width:100%; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            {{-- Status --}}
            <div>
                <label style="display:block; font-size:0.85rem; margin-bottom:0.15rem;">Status *</label>
                <select name="status"
                        style="width:100%; padding:0.4rem; border-radius:0.4rem;
                               border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                    @php
                        $currentStatus = old('status', $editingOperator->status ?? 'Active');
                    @endphp
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Admin flag --}}
            <div style="display:flex; align-items:flex-end; gap:0.35rem;">
                <label style="font-size:0.85rem;">
                    <input type="checkbox" name="is_admin" value="1"
                           {{ old('is_admin', $editingOperator->is_admin ?? false) ? 'checked' : '' }}>
                    <span style="margin-left:0.3rem;">Has admin access</span>
                </label>
            </div>
        </div>

        <div style="margin-top:0.9rem;">
            <button type="submit"
                    style="padding:0.45rem 0.9rem; border-radius:999px;
                           border:none; background:#22c55e; color:#020617;
                           font-size:0.85rem; font-weight:600; cursor:pointer;">
                {{ isset($editingOperator) ? 'Update operator' : 'Save operator' }}
            </button>
        </div>
    </form>

    {{-- Existing operators table --}}
    <section style="margin-top:1.3rem;">
        <h2 style="font-size:0.95rem; margin:0 0 0.5rem;">Operator list</h2>

        @if ($operators->isEmpty())
            <p style="font-size:0.8rem; color:#9ca3af;">
                No operators yet. Once I add records above, they’ll show here.
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
                        <th style="padding:0.55rem 0.75rem;">Callsign</th>
                        <th style="padding:0.55rem 0.75rem;">Role</th>
                        <th style="padding:0.55rem 0.75rem;">Level</th>
                        <th style="padding:0.55rem 0.75rem;">Status</th>
                        <th style="padding:0.55rem 0.75rem;">Admin</th>
                        <th style="padding:0.55rem 0.75rem; text-align:right;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($operators as $operator)
                        @php
                            $roleName   = $operator->role;
                            $roleColour = $roleName ? ($roleColours[$roleName] ?? null) : null;
                        @endphp
                        <tr style="border-top:1px solid rgba(31,41,55,0.95);">
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $operator->name }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $operator->callsign }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                @if ($roleName && $roleColour)
                                    <span style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:0.4rem;
                                        padding:0.12rem 0.45rem;
                                        border-radius:999px;
                                        border:1px solid {{ $roleColour }};
                                        background: {{ $roleColour }}1a;
                                        font-size:0.75rem;
                                    ">
                                        <span style="
                                            width:9px;
                                            height:9px;
                                            border-radius:999px;
                                            background: {{ $roleColour }};
                                            box-shadow:0 0 0 2px rgba(15,23,42,0.9);
                                        "></span>
                                        <span>{{ $roleName }}</span>
                                    </span>
                                @else
                                    {{ $roleName ?: '–' }}
                                @endif
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $operator->level }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                {{ $operator->status }}
                            </td>
                            <td style="padding:0.5rem 0.75rem;">
                                @if ($operator->is_admin)
                                    <span style="padding:0.12rem 0.4rem; border-radius:999px;
                                                 background:rgba(168,85,247,0.25);
                                                 border:1px solid rgba(168,85,247,0.9);
                                                 font-size:0.72rem;">
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td style="padding:0.5rem 0.75rem; text-align:right;">
                                <a href="{{ route('admin.operators', ['edit' => $operator->id]) }}"
                                   style="margin-right:0.5rem; color:#93c5fd; text-decoration:none;">
                                    Edit
                                </a>
                                <a href="{{ route('admin.operators.delete', $operator->id) }}"
                                   style="color:#fca5a5; text-decoration:none;"
                                   onclick="return confirm('Delete this operator record?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-pagination">
                {{ $operators->links() }}
            </div>
        @endif
    </section>
@endsection