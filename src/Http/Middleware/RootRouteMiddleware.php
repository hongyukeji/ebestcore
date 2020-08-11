<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use System\Services\CacheService;

class RootRouteMiddleware
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
        /*
        // 获取 根路由拦截 缓存
        $cache_key = CacheService::CACHE_GROUP_SYSTEM . 'routes_root_status';
        $redirect_root_status = Cache::remember($cache_key, 60 * 60, function () {
            return config('systems.routes.root.status', false) && request()->path() === '/';
        });
        (new CacheService())->foreverGroup(CacheService::CACHE_GROUP_SYSTEM, $cache_key);
        */

        // 根路由拦截
        if (request()->path() === '/') {
            // 判断是否安装
            if (!file_exists(base_path('.env'))) {
                $url = route_url('frontend.install.index');
                header("Location:{$url}");
                exit;
            }

            $redirect_root_status = boolval(config('systems.routes.root.status', false));
            if ($redirect_root_status) {
                $uri = route_url(config('systems.routes.root.uri'));
                if (!empty($uri) && $uri != '/') {
                    header("Location:{$uri}");
                    exit;
                    //return redirect($uri);
                }
            }
        }

        return $next($request);
    }
}
