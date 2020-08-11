<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GuestRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $guard
     * @param string $end
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'web', $end = 'frontend')
    {
        if (Auth::guard($guard)->check()) {
            return has_route("{$end}.index") ? redirect(route("{$end}.index")) : '/';
        }

        return $next($request);
    }
}
