@extends('layouts.app')

@section('title', 'Live Propagation Brief')

@section('content')

    @php
        /** @var \App\Models\AlertStatus|null $alertStatus */
        $alertStatus   = \App\Models\AlertStatus::query()->first();
        $alertMeta     = $alertStatus?->meta();
        $currentLevel  = $alertStatus->level ?? 5;
        $currentColour = $alertMeta['colour'] ?? '#22c55e';

        // Text colour tweak for readability (esp. Level 3 yellow)
        $textColour = '#020617';
        if (in_array($currentLevel, [1, 2, 4], true)) {
            $textColour = '#0b1120';
        }
    @endphp

    {{-- PAGE HEADER --}}
    <section style="margin-top:1.5rem; margin-bottom:1.5rem;">
        <div style="
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:flex-end;
            gap:0.75rem;
        ">
            <div>
                <h1 style="margin:0 0 0.3rem;">Live Propagation Brief</h1>
                <p style="margin:0; font-size:0.9rem; color:#9ca3af;">
                    Daily UK propagation brief and local Merseyside status, plus the global alert level used across the site.
                </p>
            </div>
        </div>
    </section>

    
    {{-- FULL-WIDTH: Daily Propagation Brief Card --}}
    <section style="
        margin-top:1.5rem;
        border-radius:0.75rem;
        border:1px solid rgba(148,163,184,0.4);
        padding:1rem 1.4rem;
        background:#020617;
    ">
        <h2 style="margin:0 0 0.6rem; font-size:1rem;">UK Propagation Brief — 2025-11-23</h2>

        <div style="font-size:0.88rem; line-height:1.55; color:#e5e7eb;">

            <p><strong>Solar/Geo:</strong> SFI about 121 sfu, sunspot count ~51. Kp currently ~2–3, forecast for next 24 h Quiet-Unsettled (Kp max ~3-4). Solar wind speed ~400–450 km/s, Bz weak and variable. Radio blackout risk: Low.<br>
            <em>Implication:</em> HF propagation mostly normal; no major enhancements.</p>

            <p><strong>HF (1.8–30 MHz):</strong> Recent ionosonde/MUF charts suggest MUF(3000 km) over UK ~15-18 MHz, peak around 17 MHz. Best daytime bands: 20 m (~14 MHz) and 17 m (~18 MHz) for UK⇄near-Europe. Evening: 40 m (~7 MHz) and 60 m (~5.3 MHz) good for NVIS. D-layer absorption low. Notable path: UK⇄EI/GM/GI on 20 m midday. NVIS window ~08:00-11:00 UTC.</p>

            <p><strong>VHF/UHF (50/70/144/432):</strong> Tropo/ducting outlook: Low — no high-pressure ridge flagged over UK. Sporadic-E: Very low (off-season). Auroral voice/data: unlikely (Kp < 4). Aircraft scatter/RS: Standard background, no special window.</p>

            <p><strong>Digital & Specials:</strong> MSK144 meteor scatter – no major shower peaks today. FT8/FT4: 20 m/17 m midday remain best for EU short-path. Satellite/contests: No major alerts or special event passes affecting today.</p>

            <p><strong>Liverpool/Merseyside Micro-Note:</strong> For Zone 10 (NW England) your best NVIS window is 40 m around 08:30-10:30 local and 60 m from ~14:30-17:30 local; 2 m/70 cm tropo or aurora to EI/GM or North Wales remains negligible today — rely HF.</p>

            <p><strong>Actionable Notes:</strong><br>
            • Try 40 m NVIS ~08:00-11:00 UTC for intra-UK nets.<br>
            • Use 20 m/17 m ~12:00-16:00 UTC for UK⇄near-Europe links.<br>
            • Standby 60 m from ~14:00-18:00 UTC for regional fallback.<br>
            • Do not count on sporadic-E or strong tropo openings today.<br>
            • Monitor Kp — if it rises to ≥4 then consider 144 MHz auroral/long-haul options.
            </p>

            <p><strong>Confidence:</strong> Medium — UK space-weather and MUF indicators align reasonably, but foF2/MUF data are inferred rather than directly measured for all paths.</p>

            <p style="font-size:0.78rem; color:#9ca3af; margin-top:1rem;">
                <strong>Sources:</strong><br>
                • Met Office Space Weather forecast: <a href="https://weather.metoffice.gov.uk/specialist-forecasts/space-weather" target="_blank" style="color:#93c5fd;">weather.metoffice.gov.uk</a><br>
                • Kp/aurora forecast: <a href="https://www.spaceweatherlive.com/en/auroral-activity/aurora-forecast.html" target="_blank" style="color:#93c5fd;">spaceweatherlive.com</a><br>
                • MUF/ionosonde description: <a href="https://www.propquest.co.uk/about.php" target="_blank" style="color:#93c5fd;">propquest.co.uk</a>
            </p>

        </div>
    </section>
@endsection