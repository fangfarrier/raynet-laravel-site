<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',          // our stub
        commands: __DIR__.'/../routes/console.php', // our stub
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aliases (route middleware)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Global web middleware (runs on every web request)
        $middleware->web(append: [
            \App\Http\Middleware\ForcePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();