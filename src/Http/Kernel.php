<?php

namespace System\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // Cookie 全局使用
        \System\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        // Session 全局使用
        \Illuminate\Session\Middleware\StartSession::class,
        // 解决跨域问题
        \Barryvdh\Cors\HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            //\App\Http\Middleware\EncryptCookies::class,
            //\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            //\Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \System\Http\Middleware\Language::class,
            // 全局拦截 根网址(/)替换为根路由
            \System\Http\Middleware\RootRouteMiddleware::class,
            \System\Http\Middleware\PluginMiddleware::class,
            // 主题中间件
            \System\Http\Middleware\ThemeMiddleware::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            // 解决跨域问题
            \Barryvdh\Cors\HandleCors::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role.admin' => \System\Http\Middleware\RoleAdminMiddleware::class,
        'permission.admin' => \System\Http\Middleware\PermissionAdminMiddleware::class,
        'auth.permission' => \System\Http\Middleware\PermissionMiddleware::class,
        'auth.admin' => \System\Http\Middleware\AuthAdmin::class,
        'auth.user' => \System\Http\Middleware\AuthUser::class,
        'guest.user' => \System\Http\Middleware\GuestRedirectIfAuthenticated::class,
        'permission.seller' => \System\Http\Middleware\PermissionSellerMiddleware::class,
        'language' => \System\Http\Middleware\Language::class,  // 语言
        'auth.jwt' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,   // JWT 认证
        'root.route' => \System\Http\Middleware\RootRouteMiddleware::class,   // 全局拦截 根网址(/)替换为根路由
        'register.intercept' => \System\Http\Middleware\RegisterInterceptMiddleware::class,   // 注册拦截器
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
