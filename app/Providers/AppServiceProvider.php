<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AlertStatus;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share the current alert status with the main layout
        View::composer('layouts.app', function ($view) {
            $current = AlertStatus::query()->orderByDesc('updated_at')->first();
            $view->with('alertStatus', $current);
        });
    }
}