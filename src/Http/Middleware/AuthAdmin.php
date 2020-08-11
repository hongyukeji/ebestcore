<?php

namespace System\Http\Middleware;

use Closure;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (auth()->guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response(trans('common.unauthorized'), 401);
            } else {
                return redirect()->guest(route('backend.auth.login'));
            }
        }

        return $next($request);
    }
}
