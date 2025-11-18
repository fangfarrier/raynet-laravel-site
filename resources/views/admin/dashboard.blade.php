@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1>Admin dashboard</h1>

    <p style="color:#9ca3af; margin-top:0.5rem;">
        Welcome to the Liverpool RAYNET admin area.
    </p>

    <ul style="margin-top:1rem; list-style:none; padding:0;">
        <li style="margin-bottom:0.5rem;">
            <a href="{{ route('admin.events') }}" style="color:#93c5fd; text-decoration:none;">
                → Manage events calendar
            </a>
        </li>
    </ul>
@endsection