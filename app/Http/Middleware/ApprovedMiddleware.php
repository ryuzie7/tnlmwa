<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user's account is approved
        if (Auth::check() && Auth::user()->approved) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Your account is not approved yet.');
    }
}
