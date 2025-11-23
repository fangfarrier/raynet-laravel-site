<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            // Optional: flash a message later; for now just 403.
            abort(403, 'Admin access only.');
        }

        return $next($request);
    }
}