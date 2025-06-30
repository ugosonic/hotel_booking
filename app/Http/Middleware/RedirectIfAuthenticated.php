<?php

namespace App\Http\Middleware;  // <-- Must match your Kernel alias

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, ...$guards)
    {
        // If user is already logged in, redirect them
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // For example, redirect to RouteServiceProvider::HOME
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
