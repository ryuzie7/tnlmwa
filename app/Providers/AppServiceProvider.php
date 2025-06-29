<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AssetRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You can bind services here if needed.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share pending asset request count with all views for admin users
        View::composer('*', function ($view) {
            $pendingRequestCount = 0;

            if (auth()->check() && auth()->user()->role === 'admin') {
                $pendingRequestCount = AssetRequest::where('status', 'pending')->count();
            }

            $view->with('pendingRequestCount', $pendingRequestCount);
        });
    }
}
