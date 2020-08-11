<?php

namespace System\Http\Middleware;

use Closure;

class PluginMiddleware
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
        event(new \System\Events\Plugins\PluginMiddlewareEvent());
        return $next($request);
    }
}
