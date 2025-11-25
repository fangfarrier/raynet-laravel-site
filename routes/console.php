<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console routes
|--------------------------------------------------------------------------
|
| This file is where you define console-only routes and the scheduler.
| The important bit for us is telling Laravel:
|   "Run condx:generate once per day at a specific time."
|
| The actual cron on the server only ever calls:
|   php artisan schedule:run
| Laravel then decides WHEN condx:generate runs.
|
*/

/**
 * Example built-in demo command from the Laravel skeleton.
 * Safe to keep; not required for the propagation brief.
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Daily UK Propagation Brief
|--------------------------------------------------------------------------
|
| This schedules our custom command:
|   php artisan condx:generate
|
| Behaviour:
| - Runs once per day at 07:10, server time adjusted to Europe/London.
| - withoutOverlapping() stops a second run starting if the first one
|   is still going (e.g. if an API hangs).
|
| You can change '07:10' to any HH:MM you prefer.
|
*/

Schedule::command('condx:generate')
    ->dailyAt('07:10')          // <-- CHANGE THIS TIME IF YOU WANT
    ->timezone('Europe/London') // Keep this in your local timezone
    ->withoutOverlapping();