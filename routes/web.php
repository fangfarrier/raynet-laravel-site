<?php

use Illuminate\Support\Facades\Route;

// Controllers I actually use in this front-end
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventAdminController;
use App\Http\Controllers\EventTypeAdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\OperatorAdminController;
use App\Http\Controllers\RoleAdminController;
use App\Http\Controllers\AlertStatusController;
use App\Http\Controllers\ProfileController; // for "My profile" page

// Models used by simple closure routes (home page)
use App\Models\Event;
use App\Models\AlertStatus;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES (NO LOGIN REQUIRED)
|--------------------------------------------------------------------------
| Note to self:
| - Keep public stuff here: home, about, event support, training, etc.
| - Avoid duplicate route declarations for the same URI.
*/

/**
 * Home page – shows next few events + current alert status.
 * This is the only place I define '/' to avoid confusion.
 */
Route::get('/', function () {
    $today = Carbon::today();

    // All future events, soonest first
    $upcoming = Event::where('starts_at', '>=', $today)
        ->orderBy('starts_at')
        ->get();

    // Global alert status for the banner / homepage tiles
    $alertStatus = AlertStatus::query()->first();

    return view('pages.home', [
        'nextEvent'   => $upcoming->first(),
        // just show two more as “coming up”
        'otherEvents' => $upcoming->slice(1, 2),
        'alertStatus' => $alertStatus,
    ]);
})->name('home');

/**
 * Static content pages – simple Blade views, no controller logic.
 */
Route::view('/about', 'pages.about')->name('about');
Route::view('/event-support', 'pages.event-support')->name('event-support');
Route::view('/training', 'pages.training')->name('training');

/**
 * Data dashboard / propagation page.
 * Note to self:
 * - These both point to the same view for now.
 * - The JS card on /members is powered from the Condx JSON; this page can
 *   later show a fuller “public dashboard”.
 */
Route::view('/data-dashboard', 'data-dashboard')->name('data-dashboard');
Route::view('/propagation', 'data-dashboard')->name('propagation'); // nice alias URL

/**
 * Public-facing support request form.
 * GET  = show form
 * POST = process + send/store request
 */
Route::get('/request-support', [SupportRequestController::class, 'create'])
    ->name('request-support');

Route::post('/request-support', [SupportRequestController::class, 'store'])
    ->name('request-support.submit');

/*
|--------------------------------------------------------------------------
| CALENDAR (PUBLIC)
|--------------------------------------------------------------------------
| Note:
| - /calendar              = HTML view
| - /calendar/{y}/{m}.ics  = calendar feed (e.g., into Outlook)
*/

Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])
    ->name('calendar');

Route::get('/calendar/{year}/{month}.ics', [CalendarController::class, 'ics'])
    ->where([
        'year'  => '[0-9]{4}',
        'month' => '[0-1][0-9]',
    ])
    ->name('calendar.ics');

/*
|--------------------------------------------------------------------------
| PUBLIC EVENTS
|--------------------------------------------------------------------------
| Note:
| - /events                        = list
| - /events/{y}/{m}/{slug}         = event detail
| - /events/{y}/{m}/{slug}.ics     = single-event iCal feed
*/

Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');

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

/*
|--------------------------------------------------------------------------
| MEMBERS AREA (AUTH REQUIRED)
|--------------------------------------------------------------------------
| Note:
| - Anything behind "auth" uses standard Laravel login (email or callsign).
| - /members         = main members’ hub
| - /change-password = password change screen (works with ForcePasswordChange)
| - /profile         = "My profile" where user can set callsign, etc.
*/

Route::middleware('auth')->group(function () {

    // Members Dashboard – this is the “home screen” once logged in
    Route::get('/members', MemberDashboardController::class)
        ->name('members');

    // Password change form – used by the ForcePasswordChange middleware
    Route::view('/change-password', 'auth.change-password')
        ->name('password.change');

    // My profile – user can change their name + callsign (with validation)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA (SEPARATE ADMIN GUARD)
|--------------------------------------------------------------------------
| Note:
| - Admin login is separate from normal user login.
| - Middleware 'admin' should check is_admin on the user (already set up).
*/

// ⬇⬇⬇ FIXED: point to showLoginForm() which actually exists
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AdminController::class, 'logout'])
    ->name('admin.logout');

Route::middleware('admin')->group(function () {

    // Admin dashboard landing page
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Global alert status – used by status card + homepage banner
    Route::post('/admin/alert-status', [AlertStatusController::class, 'update'])
        ->name('admin.alert-status.update');

    /*
    |--------------------------------------------------------------------------
    | EVENT MANAGEMENT (ADMIN)
    |--------------------------------------------------------------------------
    | Note to self:
    | - index/store/delete were already there.
    | - I've added a clean export CSV route and a pair of import routes.
    | - Avoid duplicate route names or duplicate URIs here.
    */

    // List + create events
    Route::get('/admin/events', [EventAdminController::class, 'index'])
        ->name('admin.events');

    Route::post('/admin/events', [EventAdminController::class, 'store'])
        ->name('admin.events.store');

    // Soft delete / remove event
    Route::get('/admin/events/{id}/delete', [EventAdminController::class, 'delete'])
        ->name('admin.events.delete');

    // Export all events to CSV (backup)
    // Used by the "Export all events" button on the admin list.
    Route::get(
        '/admin/events/export/csv',
        [EventAdminController::class, 'exportCsv']
    )->name('admin.events.export.csv');

    // Import events from CSV:
    // - GET  = show the upload form
    // - POST = process the uploaded CSV
    // Blade form uses route('admin.events.import.process') for the POST.
    Route::get(
        '/admin/events/import',
        [EventAdminController::class, 'showImportForm']
    )->name('admin.events.import');

    Route::post(
        '/admin/events/import',
        [EventAdminController::class, 'import']
    )->name('admin.events.import.process');

    /*
    |--------------------------------------------------------------------------
    | EVENT TYPES
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/event-types', [EventTypeAdminController::class, 'index'])
        ->name('admin.event-types');

    Route::post('/admin/event-types', [EventTypeAdminController::class, 'store'])
        ->name('admin.event-types.store');

    Route::post('/admin/event-types/{id}', [EventTypeAdminController::class, 'update'])
        ->name('admin.event-types.update');

    Route::get('/admin/event-types/{id}/delete', [EventTypeAdminController::class, 'delete'])
        ->name('admin.event-types.delete');

    /*
    |--------------------------------------------------------------------------
    | OPERATORS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/operators', [OperatorAdminController::class, 'index'])
        ->name('admin.operators');

    Route::post('/admin/operators', [OperatorAdminController::class, 'store'])
        ->name('admin.operators.store');

    Route::put('/admin/operators/{id}', [OperatorAdminController::class, 'update'])
        ->name('admin.operators.update');

    Route::get('/admin/operators/{id}/delete', [OperatorAdminController::class, 'delete'])
        ->name('admin.operators.delete');

    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/roles', [RoleAdminController::class, 'index'])
        ->name('admin.roles');

    Route::post('/admin/roles', [RoleAdminController::class, 'store'])
        ->name('admin.roles.store');

    Route::put('/admin/roles/{id}', [RoleAdminController::class, 'update'])
        ->name('admin.roles.update');

    Route::get('/admin/roles/{id}/delete', [RoleAdminController::class, 'delete'])
        ->name('admin.roles.delete');
});

/*
|--------------------------------------------------------------------------
| LARAVEL AUTH SCAFFOLDING ROUTES
|--------------------------------------------------------------------------
| Note:
| - These come from Breeze / Fortify / Jetstream.
| - They define /login, /register (if enabled), password reset, etc.
| - I have modified the LoginRequest to accept email OR callsign.
*/

require __DIR__ . '/auth.php';