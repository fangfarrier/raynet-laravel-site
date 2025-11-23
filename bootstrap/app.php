<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Web routes for the front-end site
        web: __DIR__ . '/../routes/web.php',

        // API routes (minimal use right now)
        api: __DIR__ . '/../routes/api.php',

        // Artisan console commands
        commands: __DIR__ . '/../routes/console.php',

        // Simple health check
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Route middleware aliases (used in routes as ->middleware('admin'))
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Global web middleware – runs on every web request
        $middleware->web(append: [
            \App\Http\Middleware\ForcePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Default exception handling is fine for now
    })
    ->create();