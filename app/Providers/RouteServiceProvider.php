<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard/assets';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
