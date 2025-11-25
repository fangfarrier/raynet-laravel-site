<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// NOTE TO FUTURE ME:
// - I’m using Artisan here so the dashboard can auto-generate a fresh
//   Condx brief if today’s JSON/MD files do not exist or are stale.
use Illuminate\Support\Facades\Artisan;

class CondxDashboardController extends Controller
{
    /**
     * Show the UK Propagation Brief + live-ish sliders.
     *
     * Flow:
     *  - Work out which Markdown file to show (latest.md or fallback).
     *  - Load the newest JSON payload from storage/app/condx.
     *  - If there's no JSON or it's older than today, trigger
     *    php artisan condx:generate once, then re-scan.
     *  - Derive numeric values + slider positions from that JSON.
     */
    public function show()
    {
        // ------------------------------------------------------------------
        // 1) Markdown: same logic as before (latest.md / legacy / newest .md)
        // ------------------------------------------------------------------
        $latestMarkdownPath = $this->resolveLatestMarkdownPath();

        if ($latestMarkdownPath && Storage::exists($latestMarkdownPath)) {
            $markdown = Storage::get($latestMarkdownPath);
        } else {
            // NOTE TO FUTURE ME:
            // - This is the "no brief available" fallback.
            // - In normal operation, condx:generate should mean we almost
            //   never hit this branch.
            $markdown = <<<MD
# UK Propagation Brief

_No brief is available yet. Check that `php artisan condx:generate` has run successfully._
MD;
        }

        $condxHtml = Str::markdown($markdown);

        // ------------------------------------------------------------------
        // 2) JSON: newest *.json file in storage/app/condx
        //    (with self-healing: will run condx:generate if missing/stale)
        // ------------------------------------------------------------------
        [$data, $generatedDate] = $this->loadLatestJsonData();

        // Safe defaults if JSON missing or partially missing
        $sfi        = $data['sfi']        ?? 110;
        $kpRange    = $data['kp_range']   ?? '1–2';
        $mufLow     = $data['muf_low']    ?? 12;
        $mufHigh    = $data['muf_high']   ?? 16;
        $mufPeak    = $data['muf_peak']   ?? (($mufLow + $mufHigh) / 2);
        $confidence = $data['confidence'] ?? 'Medium';

        // ------------------------------------------------------------------
        // 3) Derive compact values and slider positions (0–100%)
        // ------------------------------------------------------------------

        // Convert "2–3" → 2.5 (average Kp)
        $kpValue = $this->averageFromRange($kpRange, 0.0);

        // Aurora headline level based on Kp
        $auroraLevel = $this->auroraLevelFromKp($kpValue);

        // Slider pointer positions as percentages (clamped 2–98 so it stays on-bar)
        $auroraPointerPercent    = $this->clampPercent(($kpValue / 9) * 100);
        $kpPointerPercent        = $this->clampPercent(($kpValue / 9) * 100);

        // SFI: 0–300 mapped to 0–100 %
        $sfiPointerPercent       = $this->clampPercent(($sfi / 300) * 100);

        // MUF: 0–30 MHz mapped to 0–100 %
        $mufPointerPercent       = $this->clampPercent(($mufPeak / 30) * 100);

        // Confidence → pointer position
        $confidencePointerPercent = match (strtolower($confidence)) {
            'high'   => 20,
            'low'    => 80,
            default  => 50, // Medium / unknown
        };

        // Human-readable MUF label (e.g. "15–18 MHz")
        $mufDisplay = sprintf('%d–%d MHz', $mufLow, $mufHigh);

        // ------------------------------------------------------------------
        // 4) Pass everything to the view
        // ------------------------------------------------------------------
        return view('data-dashboard', [
            // Main brief (Markdown rendered as HTML)
            'condxHtml'      => $condxHtml,

            // Raw values
            'sfi'            => $sfi,
            'kpValue'        => $kpValue,
            'mufDisplay'     => $mufDisplay,
            'confidence'     => $confidence,
            'auroraLevel'    => $auroraLevel,
            'generatedDate'  => $generatedDate,

            // Slider pointer positions (percent 0–100)
            'auroraPointerPercent'     => $auroraPointerPercent,
            'sfiPointerPercent'        => $sfiPointerPercent,
            'kpPointerPercent'         => $kpPointerPercent,
            'mufPointerPercent'        => $mufPointerPercent,
            'confidencePointerPercent' => $confidencePointerPercent,
        ]);
    }

    // =====================================================================
    // Helper: resolve latest Markdown file for the brief
    // =====================================================================

    /**
     * Decide which Markdown file to show:
     *  1. condx/latest.md
     *  2. condx/uk-brief-latest.md
     *  3. newest *.md under condx/
     */
    protected function resolveLatestMarkdownPath(): ?string
    {
        // NOTE TO FUTURE ME:
        // - GenerateCondxBrief writes condx/latest.md as the primary target.
        if (Storage::exists('condx/latest.md')) {
            return 'condx/latest.md';
        }

        // Legacy fallback name from earlier experiments
        if (Storage::exists('condx/uk-brief-latest.md')) {
            return 'condx/uk-brief-latest.md';
        }

        // Otherwise, grab the newest Markdown file in condx/
        $mdFiles = collect(Storage::files('condx'))
            ->filter(fn ($path) => str_ends_with($path, '.md'))
            ->sort()
            ->values();

        return $mdFiles->last() ?: null;
    }

    // =====================================================================
    // Helper: load latest JSON and return [dataArray, Carbon $date]
    // =====================================================================

    /**
     * Load the latest JSON file under storage/app/condx.
     *
     * Self-healing behaviour:
     *  - If there are no JSON files at all, I call `condx:generate` once,
     *    then re-scan the folder.
     *  - If the newest JSON file is older than today, I call `condx:generate`
     *    once, then re-scan again.
     *  - If after that there's still nothing usable, I return [], today().
     */
    protected function loadLatestJsonData(): array
    {
        $today = now()->startOfDay();

        // Internal helper closure to avoid repeating the listing logic.
        $scanJson = function () {
            return collect(Storage::files('condx'))
                ->filter(fn ($path) => str_ends_with($path, '.json'))
                ->sort()
                ->values();
        };

        // First scan
        $jsonFiles = $scanJson();

        if ($jsonFiles->isEmpty()) {
            // NOTE TO FUTURE ME:
            // - No JSON yet: try to auto-generate today's brief.
            // - This calls the same artisan command I'd run from CLI:
            //     php artisan condx:generate
            Artisan::call('condx:generate');

            // Re-scan after generation
            $jsonFiles = $scanJson();

            if ($jsonFiles->isEmpty()) {
                // Still nothing → give up gracefully and use defaults.
                return [[], $today];
            }
        }

        // At this point, we have at least one JSON file.
        $latestJsonPath = $jsonFiles->last();
        $base           = basename($latestJsonPath, '.json');

        // Try to parse the YYYY-MM-DD from the filename
        try {
            $date = Carbon::parse($base)->startOfDay();
        } catch (\Throwable $e) {
            // If parsing fails, treat it as "today" for display purposes.
            $date = $today;
        }

        // If the date embedded in the filename is older than today, attempt
        // one regeneration and re-scan.
        if ($date->lt($today)) {
            Artisan::call('condx:generate');

            $jsonFiles       = $scanJson();
            $latestJsonPath  = $jsonFiles->last();
            $base            = basename($latestJsonPath, '.json');

            try {
                $date = Carbon::parse($base)->startOfDay();
            } catch (\Throwable $e) {
                $date = $today;
            }
        }

        // At this point we have a "best effort" latest JSON file.
        $raw  = Storage::get($latestJsonPath);
        $data = json_decode($raw, true) ?? [];

        return [$data, $date];
    }

    // =====================================================================
    // Helper: "2–3" → 2.5 etc.
    // =====================================================================

    protected function averageFromRange(string $range, float $fallback = 0.0): float
    {
        // Normalise any en-dash etc. to plain hyphen
        $range = str_replace('–', '-', $range);

        if (! str_contains($range, '-')) {
            // Single value like "3"
            $value = floatval(trim($range));

            return $value ?: $fallback;
        }

        [$a, $b] = array_map('trim', explode('-', $range, 2));

        $a = floatval($a);
        $b = floatval($b);

        if ($a === 0.0 && $b === 0.0) {
            return $fallback;
        }

        return ($a + $b) / 2.0;
    }

    // =====================================================================
    // Helper: map Kp → simple aurora level label
    // =====================================================================

    protected function auroraLevelFromKp(float $kp): string
    {
        if ($kp < 3) {
            return 'None';
        } elseif ($kp < 4) {
            return 'Low';
        } elseif ($kp < 6) {
            return 'Moderate';
        } elseif ($kp < 8) {
            return 'High';
        }

        return 'Storm';
    }

    // =====================================================================
    // Helper: clamp any percentage to a sane on-bar range
    // =====================================================================

    protected function clampPercent(float $value): float
    {
        // Keep pointer within the bar visually
        return max(2, min(98, round($value, 1)));
    }
}