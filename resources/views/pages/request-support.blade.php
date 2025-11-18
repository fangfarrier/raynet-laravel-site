@extends('layouts.app')

@section('title', 'Request Support')

@section('content')
    <h1>Request RAYNET Support</h1>

    @if (session('status'))
        <div style="margin-top: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem;
                    border: 1px solid rgba(34,197,94,0.6); background: rgba(22,163,74,0.15); color: #bbf7d0;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin-top: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem;
                    border: 1px solid rgba(248,113,113,0.7); background: rgba(127,29,29,0.5); color: #fecaca;">
            <strong>There were some problems with your request:</strong>
            <ul style="margin: 0.5rem 0 0 1.1rem;">
                @foreach ($errors->all() as $error)
                    <li style="margin: 0.15rem 0;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p style="max-width: 44rem; color: #9ca3af; line-height: 1.5; margin-top: 1rem;">
        Please provide as much information as you can. This helps us understand what you are planning and how
        Liverpool RAYNET can best support your event.
    </p>

    <div style="margin-top: 1.5rem; padding: 1.5rem; border-radius: 0.75rem;
                border: 1px solid rgba(148,163,184,0.4); background: #020819;">
        <form method="post" action="{{ route('request-support.submit') }}">
            @csrf

            <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <div>
                    <label for="event_name" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Event name *</label>
                    <input id="event_name" name="event_name" type="text"
                           value="{{ old('event_name') }}"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
                <div>
                    <label for="event_date" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Event date</label>
                    <input id="event_date" name="event_date" type="date"
                           value="{{ old('event_date') }}"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
            </div>

            <div style="margin-top: 1rem; display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <div>
                    <label for="location" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Location *</label>
                    <input id="location" name="location" type="text"
                           value="{{ old('location') }}"
                           placeholder="Area, start/finish, key sites…"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
                <div>
                    <label for="org" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Organising body</label>
                    <input id="org" name="org" type="text"
                           value="{{ old('org') }}"
                           placeholder="Charity, council, club…"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label for="contact_name" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Primary contact name *</label>
                <input id="contact_name" name="contact_name" type="text"
                       value="{{ old('contact_name') }}"
                       style="width:100%; max-width:420px; padding:0.4rem; border-radius:0.4rem;
                              border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
            </div>

            <div style="margin-top: 1rem; display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <div>
                    <label for="contact_email" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Contact email *</label>
                    <input id="contact_email" name="contact_email" type="email"
                           value="{{ old('contact_email') }}"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
                <div>
                    <label for="contact_phone" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">Contact phone</label>
                    <input id="contact_phone" name="contact_phone" type="text"
                           value="{{ old('contact_phone') }}"
                           style="width:100%; padding:0.4rem; border-radius:0.4rem;
                                  border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label for="details" style="display:block; font-size:0.9rem; margin-bottom:0.2rem;">
                    Brief outline of the event and where you think RAYNET would help *
                </label>
                <textarea id="details" name="details" rows="5"
                          style="width:100%; padding:0.5rem; border-radius:0.4rem;
                                 border:1px solid rgba(148,163,184,0.7); background:#020617; color:#e5e7eb;">{{ old('details') }}</textarea>
            </div>

            <button type="submit"
                    style="margin-top: 1.25rem; padding: 0.5rem 1.2rem; border-radius: 999px;
                           border: 1px solid rgba(56,189,248,0.7); background: #020617;
                           color:#e5e7eb; font-size:0.9rem;">
                Submit request
            </button>
        </form>
    </div>
@endsection