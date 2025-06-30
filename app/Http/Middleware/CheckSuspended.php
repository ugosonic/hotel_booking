<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->status === 'suspended') {
                // redirect to a special “Suspended” page
                return redirect()->route('suspended.notice');
            }
        }
        return $next($request);
    }
}
