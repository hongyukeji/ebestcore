<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthUser
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
        if (!Auth::guard($guard)->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            return has_route("{$end}.auth.login") ? redirect(route("{$end}.auth.login")) : redirect()->back();
        }

        // 三个判断：
        // 1. 如果用户已经登录
        // 2. 并且还未认证 Email
        // 3. 并且访问的不是 email 验证相关 URL 或者退出的 URL。

        // 如果是web会员登录
        if (Auth::guard('web')->check()) {

            // 判断邮箱验证是否开启 邮箱是否验证
            if (config('params.intercept.email_verify', false) &&
                !$request->user()->hasVerifiedEmail() && !$request->is(
                    str_before(route('frontend.auth.verification.email.notice', [], false), 'verify') . '*',
                    route('frontend.auth.logout', [], false)
                )) {
                // 根据客户端返回对应的内容
                return $request->expectsJson()
                    ? abort(403, '您的电子邮件地址未验证。')
                    : redirect()->route("{$end}.auth.verification.email.notice");
            }

            // 判断手机号验证是否开启 手机号是否验证
            if (config('params.intercept.mobile_verify', false) &&
                !$request->user()->hasVerifiedMobile() && !$request->is(
                    str_before(route('frontend.auth.verification.mobile.notice', [], false), 'verify') . '*',
                    route('frontend.auth.logout', [], false)
                )) {
                return $request->expectsJson()
                    ? abort(403, '您的手机号未验证。')
                    : redirect()->route("{$end}.auth.verification.mobile.notice");
            }

        }

        return $next($request);
    }
}
