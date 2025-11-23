<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| This file boots the Laravel 12 application. The important bits:
|  - withRouting(...) tells Laravel where your route files are.
|  - withMiddleware(...) registers global middleware & aliases.
|  - withExceptions(...) lets you customise exception handling.
|
| If routes/web.php isn't wired up here, everything 404s.
|
*/

return Application::configure(
    basePath: dirname(__DIR__),
)->withRouting(
    // Web routes – this is what serves '/', /admin, /members etc.
    web: __DIR__ . '/../routes/web.php',

    // API routes – you may or may not use these yet, but keep them wired.
    api: __DIR__ . '/../routes/api.php',

    // Artisan console routes (for scheduled/console-only commands).
    commands: __DIR__ . '/../routes/console.php',

    // Optional healthcheck endpoint (Laravel 12 default).
    health: '/up',
)->withMiddleware(function (Middleware $middleware) {
    /*
    |--------------------------------------------------------------------------
    | Global HTTP Middleware
    |--------------------------------------------------------------------------
    |
    | You can push or append global middleware here. For now, we just make
    | sure your custom ForcePasswordChange middleware runs for normal
    | web requests, AFTER the session/auth layers.
    |
    */

    // This runs for every request in the "web" stack.
    $middleware->append(\App\Http\Middleware\ForcePasswordChange::class);

    /*
    |--------------------------------------------------------------------------
    | Route Middleware Aliases
    |--------------------------------------------------------------------------
    |
    | These aliases are used in routes/web.php, e.g.
    | Route::middleware('admin')->group(...)
    |
    */

    $middleware->alias([
        // Standard auth aliases
        'auth'     => \App\Http\Middleware\Authenticate::class,
        'guest'    => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Your custom admin gate – checks is_admin on the logged-in user
        'admin'    => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})->withExceptions(function (Exceptions $exceptions) {
    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    |
    | Leave this empty for now; you can add custom render/report hooks later
    | if you want to log things differently or show custom error pages.
    |
    */
})->create();