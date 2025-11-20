<!DOCTYPE html>
<html lang="en">
<style>
    /* Fix oversized Laravel pagination in admin tables */
    .admin-pagination nav > div > span,
    .admin-pagination nav > div > a {
        all: unset;
        font-size: 0.85rem;
        color: #93c5fd;
        cursor: pointer;
    }

    .admin-pagination nav > div {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 0;
        color: #e5e7eb;
    }

    .admin-pagination nav {
        margin-top: 1rem;
    }
</style>
<head>
    <meta charset="UTF-8">
    <title>Liverpool RAYNET – @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/web-app-manifest-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/web-app-manifest-512x512.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="theme-color" content="#020617">

    <style>
        :root {
            --bg: #020617;
            --bg-alt: #02081a;
            --nav: #020617;
            --border: rgba(148, 163, 184, 0.35);
            --accent: #38bdf8;
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;

            --raynet-blue: #1d4ed8;
            --raynet-blue-bg: rgba(37,99,235,0.25);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #020617 0, #000 60%);
            color: var(--text-main);
        }

        .site-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(2, 6, 23, 0.96);
            border-bottom: 1px solid rgba(148, 163, 184, 0.3);
            backdrop-filter: blur(10px);
        }

        .nav-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand-block {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .brand-meta {
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            gap: 1.1rem;
            font-size: 0.9rem;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            padding-bottom: 0.1rem;
            border-bottom: 1px solid transparent;
        }

        .nav-links a:hover {
            color: var(--accent);
            border-bottom-color: rgba(56, 189, 248, 0.7);
        }

        .nav-links a.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        @media (max-width: 720px) {
            .nav-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-wrap: wrap;
            }
        }

        .content-wrap {
            flex: 1;
            max-width: 1120px;
            margin: 0 auto;
            padding: 2rem 1.5rem 2.5rem;
        }

        .footer {
            border-top: 1px solid rgba(148, 163, 184, 0.25);
            background: #020617;
        }

        .footer-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 1rem 1.5rem 1.3rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .footer-inner a {
            color: var(--accent);
            text-decoration: none;
        }

        .footer-inner a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="site-shell">

    @php
        /** @var \App\Models\AlertStatus|null $alertStatus */
        $alertStatus = \App\Models\AlertStatus::query()->first();
    @endphp

    {{-- NAVIGATION BAR --}}
    <nav class="navbar">
        <div class="nav-inner">
            <div class="brand-block">
                <span class="brand-title">Liverpool RAYNET</span>
                <span class="brand-meta">Zone 10 · Merseyside · Group 179</span>
            </div>

            <div class="nav-links">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('event-support') }}" class="{{ request()->routeIs('event-support') ? 'active' : '' }}">Event Support</a>
                <a href="{{ route('request-support') }}" class="{{ request()->routeIs('request-support') ? 'active' : '' }}">Request Support</a>
                <a href="{{ route('training') }}" class="{{ request()->routeIs('training') ? 'active' : '' }}">Training</a>
                <a href="{{ route('members') }}" class="{{ request()->routeIs('members') ? 'active' : '' }}">Members</a>
            </div>
        </div>
    </nav>

    {{-- GLOBAL ALERT BANNER (levels 1–3) --}}
    @if(isset($alertStatus) && $alertStatus && ($meta = $alertStatus->meta()) && in_array($alertStatus->level, [1,2,3]))
        @php
            $colour = $meta['colour'] ?? '#facc15';
        @endphp
        <div style="
            border-bottom:1px solid rgba(15,23,42,0.9);
            background: linear-gradient(to right, {{ $colour }}, rgba(15,23,42,0.98));
        ">
            <div style="
                max-width:1120px;
                margin:0 auto;
                padding:0.55rem 1.5rem;
                display:flex;
                flex-wrap:wrap;
                gap:0.6rem;
                align-items:flex-start;
                font-size:0.82rem;
                color:#0b1120;
            ">
                <div style="
                    display:inline-flex;
                    align-items:center;
                    gap:0.4rem;
                    padding:0.18rem 0.55rem;
                    border-radius:999px;
                    border:1px solid rgba(15,23,42,0.5);
                    background:rgba(15,23,42,0.08);
                    font-size:0.75rem;
                    text-transform:uppercase;
                    letter-spacing:0.08em;
                    font-weight:700;
                ">
                    <span style="
                        width:9px; height:9px; border-radius:999px;
                        background:#dc2626; box-shadow:0 0 0 3px rgba(248,250,252,0.6);
                    "></span>
                    <span>{{ $meta['title'] }}</span>
                </div>

                <div style="flex:1; min-width:220px; color:#f9fafb;">
                    @if(!empty($alertStatus->headline))
                        <div style="font-weight:600; margin-bottom:0.1rem;">
                            {{ $alertStatus->headline }}
                        </div>
                    @endif
                    <div style="opacity:0.95;">
                        {{ $alertStatus->message ?: $meta['description'] }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PAGE CONTENT --}}
    <main class="content-wrap">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="footer-inner">
            <span>&copy; {{ date('Y') }} Liverpool RAYNET (10/ME/179/). All rights reserved.</span>
            <span>Affiliated to RAYNET-UK · Volunteer emergency communications for Merseyside.</span>
        </div>

        @if(isset($alertStatus) && $alertStatus && ($meta = $alertStatus->meta()))
            <div style="
                max-width:1120px;
                margin:0 auto 0.5rem;
                padding:0 1.5rem 0.5rem;
                font-size:0.8rem;
                color:#9ca3af;
            ">
                <div style="display:flex; flex-wrap:wrap; gap:0.4rem; align-items:center;">
                    <span style="font-weight:500; color:#e5e7eb;">Current alert status:</span>
                    <span style="
                        display:inline-flex;
                        align-items:center;
                        gap:0.4rem;
                        padding:0.12rem 0.55rem;
                        border-radius:999px;
                        border:1px solid {{ $meta['colour'] }};
                        background: {{ $meta['colour'] }}1a;
                        color:#e5e7eb;
                    ">
                        <span style="
                            width:9px; height:9px; border-radius:999px;
                            background:{{ $meta['colour'] }};
                            box-shadow:0 0 0 2px rgba(15,23,42,0.7);
                        "></span>
                        <span>{{ $meta['title'] }}</span>
                    </span>
                    @if(!empty($alertStatus->headline))
                        <span style="color:#cbd5f5;">— {{ $alertStatus->headline }}</span>
                    @endif
                </div>
            </div>
        @endif
    </footer>

</div>

@if (session('is_admin') === true)
    {{-- Show ADMIN badge when authenticated --}}
    <a href="{{ route('admin.dashboard') }}"
       style="
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 2000;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            background: linear-gradient(to right, #a855f7, #7e22ce);
            color: #fff;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 6px 18px rgba(0,0,0,0.35);
       ">
        ADMIN
    </a>
@else
    {{-- Always leave a tiny login button visible in case admin session is lost --}}
    <a href="{{ route('admin.login') }}"
       style="
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 2000;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            background: #334155;
            color: #fff;
            font-size: 0.7rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
       ">
        Admin Login
    </a>
@endif

{{-- MEMBER BADGE --}}
<a href="{{ route('members') }}"
   style="
        position: fixed;
        top: 1rem;
        right: 6rem;
        z-index: 1999;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        background: var(--raynet-blue-bg);
        border: 1px solid var(--raynet-blue);
        color: #dbeafe;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
   ">
    MEMBER
</a>

</body>
</html>