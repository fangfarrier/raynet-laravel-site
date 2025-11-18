<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventAdminController;
use App\Http\Controllers\EventTypeAdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MembersController;
use App\Models\Event;
use Illuminate\Support\Carbon;


// ----------------------
// HOME – with next event
// ----------------------
Route::get('/', function () {
    $today = Carbon::today();

    $upcoming = Event::where('starts_at', '>=', $today)
        ->orderBy('starts_at')
        ->get();

    return view('pages.home', [
        'nextEvent'   => $upcoming->first(),
        'otherEvents' => $upcoming->slice(1, 2),
    ]);
})->name('home');


// ----------------------
// STATIC PAGES
// ----------------------
Route::view('/about', 'pages.about')->name('about');
Route::view('/event-support', 'pages.event-support')->name('event-support');
Route::view('/training', 'pages.training')->name('training');

/**
 * Reminder to self: Members now use a controller so I can pass data in.
 */
Route::get('/members', [MembersController::class, 'index'])
    ->name('members');


// ----------------------
// REQUEST SUPPORT (Form)
// ----------------------
Route::get('/request-support', [SupportRequestController::class, 'create'])
    ->name('request-support');

Route::post('/request-support', [SupportRequestController::class, 'store'])
    ->name('request-support.submit');


// ----------------------
// CALENDAR
// ----------------------
Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])
    ->name('calendar');

Route::get('/calendar/{year}/{month}.ics', [CalendarController::class, 'ics'])
    ->where([
        'year'  => '[0-9]{4}',
        'month' => '[0-1][0-9]',
    ])
    ->name('calendar.ics');


// ----------------------
// PUBLIC EVENTS
// ----------------------
/**
 * Reminder to self: List view of events for embedding and member hub links.
 */
Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');

// /events/{year}/{month}/{slug}
// /events/{year}/{month}/{slug}.ics
Route::get('/events/{year}/{month}/{slug}', [EventController::class, 'show'])
    ->where([
        'year'  => '[0-9]{4}',
        'month' => '[0-1][0-9]',
        'slug'  => '[A-Za-z0-9\-]+',
    ])
    ->name('events.show');

Route::get('/events/{year}/{month}/{slug}.ics', [EventController::class, 'ics'])
    ->where([
        'year'  => '[0-9]{4}',
        'month' => '[0-1][0-9]',
        'slug'  => '[A-Za-z0-9\-]+',
    ])
    ->name('events.ics');


// ----------------------
// ADMIN LOGIN
// ----------------------
Route::get('/admin/login', [AdminController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminController::class, 'logout'])
    ->name('admin.logout');


// ----------------------
// PROTECTED ADMIN AREA
// ----------------------
Route::middleware('admin')->group(function () {

    // Admin dashboard
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // EVENT ADMIN
    Route::get('/admin/events', [EventAdminController::class, 'index'])
        ->name('admin.events');

    Route::post('/admin/events', [EventAdminController::class, 'store'])
        ->name('admin.events.store');

    Route::get('/admin/events/{id}/delete', [EventAdminController::class, 'delete'])
        ->name('admin.events.delete');

    // EVENT TYPES ADMIN
    Route::get('/admin/event-types', [EventTypeAdminController::class, 'index'])
        ->name('admin.event-types');

    Route::post('/admin/event-types', [EventTypeAdminController::class, 'store'])
        ->name('admin.event-types.store');

    Route::post('/admin/event-types/{id}', [EventTypeAdminController::class, 'update'])
        ->name('admin.event-types.update');

    Route::get('/admin/event-types/{id}/delete', [EventTypeAdminController::class, 'delete'])
        ->name('admin.event-types.delete');
});