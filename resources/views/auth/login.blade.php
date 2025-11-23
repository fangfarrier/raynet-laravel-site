@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-slate-900 border border-slate-700 rounded-lg p-6">
        <h1 class="text-xl font-semibold mb-4">Member login</h1>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-400">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm mb-1">Email address</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-slate-100">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm mb-1">Password</label>
                <input id="password" name="password" type="password" required
                       autocomplete="current-password"
                       class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-slate-100">
            </div>

            <div class="mb-4 flex items-center">
                <input id="remember" name="remember" type="checkbox"
                       class="mr-2 rounded border-slate-500 bg-slate-900">
                <label for="remember" class="text-sm text-slate-300">Remember me</label>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium">
                    Log in
                </button>
            </div>
        </form>
    </div>
@endsection