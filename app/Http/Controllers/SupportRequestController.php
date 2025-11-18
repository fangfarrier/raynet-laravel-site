<?php

namespace App\Http\Controllers;

use App\Mail\SupportRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportRequestController extends Controller
{
    public function create()
    {
        return view('pages.request-support');
    }

    public function store(Request $request)
    {
        // 1. Validate input
        $data = $request->validate([
            'event_name'    => ['required', 'string', 'max:255'],
            'event_date'    => ['nullable', 'date'],
            'location'      => ['required', 'string', 'max:255'],
            'org'           => ['nullable', 'string', 'max:255'],
            'contact_name'  => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'details'       => ['required', 'string'],
        ]);

        // 2. Send email to you (adjust address as needed)
        Mail::to('G4BDS@raynet-liverpool.uk')
            ->send(new SupportRequestSubmitted($data));

        // 3. Redirect with success message
        return redirect()
            ->route('request-support')
            ->with('status', 'Thank you – your request has been sent. We will review it and get back to you.');
    }
}