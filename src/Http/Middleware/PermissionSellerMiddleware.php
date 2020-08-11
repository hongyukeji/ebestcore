<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use System\Models\Shop;

class PermissionSellerMiddleware
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
        if (Auth::guard()->check()) {
            // 判断当前用户是否存在店铺
            $user_id = Auth::id();
            $shop = Shop::query()->where('user_id', $user_id)->first();
            if ($shop && $shop->audit_status == Shop::AUDIT_STATUS_PASS) {
                return $next($request);
            }
        }

        return has_route("seller.apply.index") ? redirect(route("seller.apply.index")) : redirect()->back();
    }
}
