@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

    @php
        // Short-hands for the view, so I don’t keep typing $user->...
        /** @var \App\Models\User $user */
        $userName     = $user->name;
        $userEmail    = $user->email;
        $userCallsign = $user->callsign;
    @endphp

    {{-- PAGE HEADER --}}
    <section style="margin-bottom: 1.5rem;">
        <h1 style="margin:0 0 0.4rem; font-size:1.25rem; color:#e5e7eb;">
            My profile
        </h1>
        <p style="margin:0; font-size:0.9rem; color:#9ca3af;">
            Manage how I appear in Liverpool RAYNET systems – name, callsign and account details.
        </p>
    </section>

    {{-- STATUS BANNER (success + validation errors) --}}
    @if (session('status'))
        <div style="
            margin-bottom:0.9rem;
            padding:0.6rem 0.8rem;
            border-radius:0.75rem;
            border:1px solid rgba(34,197,94,0.7);
            background:rgba(22,163,74,0.18);
            color:#bbf7d0;
            font-size:0.85rem;
        ">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="
            margin-bottom:0.9rem;
            padding:0.6rem 0.8rem;
            border-radius:0.75rem;
            border:1px solid rgba(248,113,113,0.7);
            background:rgba(220,38,38,0.18);
            color:#fecaca;
            font-size:0.85rem;
        ">
            <strong style="display:block; margin-bottom:0.3rem;">Please fix the following:</strong>
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- LAYOUT: LEFT = form, RIGHT = account summary --}}
    <section style="
        display:flex;
        flex-wrap:wrap;
        gap:1.4rem;
        align-items:flex-start;
    ">
        {{-- LEFT: EDIT FORM --}}
        <article style="
            flex:1 1 320px;
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background:rgba(15,23,42,0.96);
            padding:1rem 1.2rem 1rem;
            font-size:0.9rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.6rem; font-size:1rem; color:#e5e7eb;">
                Profile details
            </h2>
            <p style="margin:0 0 0.7rem; font-size:0.8rem; color:#9ca3af;">
                These details are used across the members’ hub, event rosters and training systems.
            </p>

            {{-- NOTE TO FUTURE ME:
               - This form now updates name, email and callsign.
               - Route is PATCH /profile (see web.php), hence @method('patch').
            --}}
            <form method="POST" action="{{ route('profile.update') }}" style="margin-top:0.4rem;">
                @csrf
                @method('patch')

                {{-- Name --}}
                <div style="margin-bottom:0.7rem;">
                    <label for="name" style="display:block; font-size:0.82rem; margin-bottom:0.15rem;">
                        Full name
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $userName) }}"
                        required
                        style="
                            width:100%;
                            padding:0.45rem 0.55rem;
                            border-radius:0.6rem;
                            border:1px solid rgba(148,163,184,0.7);
                            background:#020617;
                            color:#e5e7eb;
                            font-size:0.9rem;
                        "
                    >
                    <p style="margin:0.2rem 0 0; font-size:0.75rem; color:#6b7280;">
                        This is how your name appears on members’ pages and admin lists.
                    </p>
                </div>

                {{-- Email (now editable, previously disabled) --}}
                {{-- NOTE TO FUTURE ME:
                   - Must have name="email" or the controller never sees it.
                   - We use old('email', ...) so validation errors keep the user's input.
                --}}
                <div style="margin-bottom:0.7rem;">
                    <label for="email" style="display:block; font-size:0.82rem; margin-bottom:0.15rem;">
                        Email address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $userEmail) }}"
                        required
                        style="
                            width:100%;
                            padding:0.45rem 0.55rem;
                            border-radius:0.6rem;
                            border:1px solid rgba(148,163,184,0.7);
                            background:#020617;
                            color:#e5e7eb;
                            font-size:0.9rem;
                        "
                    >
                    <p style="margin:0.2rem 0 0; font-size:0.75rem; color:#6b7280;">
                        This is your login email. Changing it may trigger re-verification.
                    </p>
                </div>

                {{-- Callsign --}}
                {{-- NOTE TO FUTURE ME:
                   - Callsign is optional but recommended.
                   - Validation pattern is enforced in ProfileUpdateRequest.
                --}}
                <div style="margin-bottom:0.9rem;">
                    <label for="callsign" style="display:block; font-size:0.82rem; margin-bottom:0.15rem;">
                        Callsign
                    </label>
                    <input
                        id="callsign"
                        name="callsign"
                        type="text"
                        value="{{ old('callsign', $userCallsign) }}"
                        placeholder="e.g. G4BDS"
                        style="
                            width:100%;
                            padding:0.45rem 0.55rem;
                            border-radius:0.6rem;
                            border:1px solid rgba(96,165,250,0.9);
                            background:#020617;
                            color:#e5e7eb;
                            font-size:0.9rem;
                            text-transform:uppercase;
                        "
                    >
                    <p style="margin:0.2rem 0 0; font-size:0.75rem; color:#6b7280;">
                        Optional, but recommended. 3–10 characters, letters/numbers/slash only.
                        Used for logging and event paperwork.
                    </p>
                </div>

                {{-- Actions --}}
                <div style="display:flex; flex-wrap:wrap; gap:0.6rem; align-items:center;">
                    <button type="submit" style="
                        padding:0.45rem 0.95rem;
                        border-radius:999px;
                        border:none;
                        background:linear-gradient(to right,#38bdf8,#0ea5e9);
                        color:#020617;
                        font-size:0.9rem;
                        font-weight:600;
                        cursor:pointer;
                    ">
                        Save changes
                    </button>

                    <a href="{{ route('password.change') }}"
                       style="font-size:0.8rem; color:#93c5fd; text-decoration:none;">
                        Change my password →
                    </a>
                </div>
            </form>
        </article>

        {{-- RIGHT: ACCOUNT SNAPSHOT / CONTEXT --}}
        <article style="
            flex:0 0 280px;
            max-width:320px;
            border-radius:1rem;
            border:1px solid rgba(148,163,184,0.5);
            background:radial-gradient(circle at top left,#020617,#020617 60%,#020617 100%);
            padding:1rem 1rem 0.9rem;
            font-size:0.85rem;
            color:#e5e7eb;
        ">
            <h2 style="margin:0 0 0.5rem; font-size:1rem;">
                Account summary
            </h2>

            <p style="margin:0 0 0.6rem; font-size:0.8rem; color:#9ca3af;">
                Quick snapshot of how this account is seen by Liverpool RAYNET systems.
            </p>

            <dl style="margin:0; font-size:0.83rem;">
                <div style="margin-bottom:0.4rem;">
                    <dt style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">
                        Name
                    </dt>
                    <dd style="margin:0; font-weight:500;">
                        {{ $userName }}
                    </dd>
                </div>

                <div style="margin-bottom:0.4rem;">
                    <dt style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">
                        Email
                    </dt>
                    <dd style="margin:0;">
                        {{ $userEmail }}
                    </dd>
                </div>

                <div style="margin-bottom:0.4rem;">
                    <dt style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">
                        Callsign
                    </dt>
                    <dd style="margin:0;">
                        {{ $userCallsign ?: 'Not set' }}
                    </dd>
                </div>

                <div style="margin-bottom:0.4rem;">
                    <dt style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">
                        Member since
                    </dt>
                    <dd style="margin:0;">
                        {{ optional($user->created_at)->format('d M Y') ?? 'Unknown' }}
                    </dd>
                </div>
            </dl>

            <p style="margin:0.8rem 0 0; font-size:0.78rem; color:#6b7280;">
                Over time this panel can pull in your training level, exercise history
                and typical deployment roles so controllers get an instant picture.
            </p>
        </article>
    </section>
@endsection