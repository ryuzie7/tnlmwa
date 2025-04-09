<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // app/Http/Middleware/ApprovedUser.php

    public function handle($request, Closure $next)
    {
        if (auth()->user()->role === 'custodian' && !auth()->user()->approved) {
            abort(403, 'Awaiting admin approval.');
        }

        return $next($request);
    }

}
