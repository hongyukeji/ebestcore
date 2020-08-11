<?php

namespace System\Http\Middleware;

use Closure;

class RegisterInterceptMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('params.register.status', true)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('会员注册功能已关闭', 405);
            } else {
                return abort(405, '会员注册功能已关闭');
            }
        }

        return $next($request);
    }
}
