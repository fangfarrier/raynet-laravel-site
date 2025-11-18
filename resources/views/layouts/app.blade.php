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

    <style>
        :root {
            --bg: #020617;
            --bg-alt: #02081a;
            --nav: #020617;
            --border: rgba(148, 163, 184, 0.35);
            --accent: #38bdf8;
            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
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

    <main class="content-wrap">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <span>&copy; {{ date('Y') }} Liverpool RAYNET (10/ME/179/). All rights reserved.</span>
            <span>Affiliated to RAYNET-UK · Volunteer emergency communications for Merseyside.</span>
        </div>
    </footer>

</div>
</body>
</html>