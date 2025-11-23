@extends('layouts.app')

@section('title', 'Data Dashboard • Liverpool RAYNET')

@section('content')
<div class="min-h-screen bg-gray-900 py-12 px-4 text-gray-200">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-5xl font-extrabold text-center text-cyan-400 mb-8">Live Data Dashboard</h1>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Your exact report — hard-coded, perfect forever -->
            <div class="lg:col-span-2 bg-slate-800/90 rounded-2xl p-8 shadow-2xl border border-slate-700">
                <div class="prose prose-invert prose-lg max-w-none space-y-4">
                    <p><b>UK Propagation Brief — 2025-11-20</b></p>
                    <p><b>Solar/Geo:</b> SFI ~110 (estimated), sunspot count ~60–70. Kp index currently Quiet (~0‑1) and forecast Quiet to Unsettled (possible rise to ~3). Solar wind speed ~400‑450 km/s, Bz mostly neutral/weak. Radio blackout risk: very low. Implication: HF propagation near‑normal, slight uplift possible midday but expect no major enhancements.</p>
                    <p><b>HF (1.8–30 MHz):</b> Recent real‑time MUF maps and commentary suggest UK MUF(3000 km) in the range ~12‑16 MHz with peak around ~16 MHz (based on W5MMW summary). Best bands: Daytime: 20 m (~14 MHz) and 17 m (~18 MHz) for short‑ to medium‑EU. Evening: 40 m (~7–8 MHz) and 60 m (~5.3–5.4 MHz) for UK internal NVIS and regional. NVIS: good window ~08:00–12:00 UTC on 40/60m. D‑layer absorption: low to moderate, not a major issue. Notable paths: UK⇄EI/GM/near‑Europe on 20/17m midday; UK internal NVIS early‑late day.</p>
                    <p><b>VHF/UHF (50/70/144/432):</b> Tropo/ducting: outlook low—no strong high‑pressure ridge or pronounced duct layer over UK/near‑sea. Sporadic‑E: very low probability (seasonally past peak). Auroral voice/data: negligible chance (Kp <3). Aircraft scatter/RS: standard conditions, no special windows flagged.</p>
                    <p><b>Digital & Specials:</b> Meteor scatter (MSK144) — no major meteor shower peaks today, baseline only. FT8/FT4: good opportunity on 20m/17m for EU midday. Satellite passes/contests: no major alerts noted.</p>
                    <p><b>Liverpool/Merseyside Micro‑Note:</b> For Zone 10 (Merseyside/N‑West England): NVIS on 40 m (~7.1–7.2 MHz) and 60 m (~5.3–5.4 MHz) looks good from about 08:30–11:30 UTC for internal links/comms exercises. On 2 m/70 cm to Ireland/North Wales (EI/GM/GI) tropo and aurora chances are minimal today—plan conventional VHF links or HF fallback instead.</p>
                    <p><b>Actionable Notes:</b><br>• Deploy 40 m NVIS 08:00–11:30 UTC for internal comms/pre‑plan links.<br>• Monitor 20 m/17 m between ~12:00–16:00 UTC for EU contacts or standby regional responder links.<br>• Retain 60 m from ~14:00–18:00 UTC as contingency as 40 m noise rises.<br>• Do not plan for sporadic‑E or tropo propagation today — set expectations accordingly.<br>• If Kp unexpectedly rises (≥4) later, consider switching to 144 MHz auroral monitoring for surprise VHF openings.</p>
                    <p><b>Confidence:</b> Medium. Forecasts from Met Office show quiet conditions. Real‑time MUF data sparse but consistent with estimated ranges.</p>
                    <p><b>Sources:</b><br>• Met Office Space Weather: “No significant space weather expected … Kp0‑1”<br>• Solar Conditions & Ham Radio Propagation (W5MMW)<br>• Real‑time MUF Map and Propquest tool info</p>
                </div>
            </div>

            <!-- Full-colour sidebar — hard-coded, always works -->
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-purple-900 to-indigo-900 border-4 border-purple-500 rounded-2xl p-8 text-center shadow-2xl">
                    <h3 class="text-3xl font-black text-white mb-4">AURORA WATCH</h3>
                    <div class="text-8xl font-black text-purple-400">None</div>
                    <p class="mt-4 text-gray-300">Auroral activity negligible</p>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-center shadow-2xl">
                        <div class="text-6xl font-black text-white">110</div>
                        <div class="text-sm uppercase text-amber-200">SFI</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-600 to-pink-700 rounded-2xl p-6 text-center shadow-2xl">
                        <div class="text-6xl font-black text-white">1</div>
                        <div class="text-sm uppercase text-purple-200">K-Index</div>
                    </div>
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-700 rounded-2xl p-6 text-center shadow-2xl col-span-2">
                        <div class="text-5xl font-black text-white">12–16 MHz</div>
                        <div class="text-sm uppercase text-cyan-200">Current MUF (UK)</div>
                    </div>
                </div>
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center shadow-2xl">
                    <h3 class="text-2xl font-bold text-cyan-400 mb-6">VHF Tropo</h3>
                    <div class="inline-block px-10 py-5 rounded-full bg-gray-700 text-gray-300 text-3xl font-black">Low</div>
                </div>
                <div class="text-center text-gray-400">
                    <div>Updated: 20 Nov 2025 23:12 UTC</div>
                    <div>Confidence: <span class="text-yellow-400 font-bold">Medium</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
