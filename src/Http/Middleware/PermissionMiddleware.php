<?php

namespace System\Http\Middleware;

use System\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $guard = 'admin', $resource = null)
    {
        if (!auth($guard)->check()) {
            throw new UnauthorizedException(trans('backend.messages.login_tips'), '401');
        }

        if ($guard == 'admin' && auth($guard)->user()->isSuperAdmin()) {
            return $next($request);
        }

        // 超级管理员不需要验证
        $super_admin_role = config('systems.security.administrator', 'Administrator');
        if (!auth($guard)->user()->hasrole($super_admin_role)) {

            // Controller Method
            $permission = $this->getPermission($resource);
            $hasPermission = $this->hasPermission($permission, $guard);

            // Namespace
            $namespace_permission = $this->getNamespacePermission();
            $hasNamespacePermission = $this->hasPermission($namespace_permission, $guard);

            // Url
            $url_permission = str_start($this->getUrlPermission($request), '/');
            $hasUrlPermission = $this->hasUrlPermission($url_permission, $guard);

            // 判断是否包含当前Url
            if (auth($guard)->user()->hasAnyPermission($url_permission)) {
                return $next($request);
            }

            // 判断是否是命名空间
            if (auth($guard)->user()->hasAnyPermission($namespace_permission)) {
                return $next($request);
            }

            // 判断是否包含当前控制器
            if (auth($guard)->user()->hasAnyPermission($permission)) {
                return $next($request);
            }

            // 权限规则没有定义处理
            if (!$hasPermission && !$hasNamespacePermission && !$hasUrlPermission) {
                // 判断是否开启权限检查严格模式
                if (config('systems.security.strict_check_mode', false) == false) {
                    return $next($request);
                }
            }

            // 没有权限
            throw new UnauthorizedException(trans('backend.messages.not_have_handle_permission'), '403');
        }

        return $next($request);
    }

    /**
     * 权限规则存在地验证
     *
     * @param string $permission
     * @param string $guard
     *
     * @return bool
     */
    protected function hasPermission(string $permission, string $guard): bool
    {
        $where = [
            ['name', '=', $permission],
            ['guard_name', '=', $guard],
        ];
        $has = DB::table(config('permission.table_names.permissions', 'permissions'))->where($where)->first();
        return boolval($has);
    }

    /**
     * 根据路由获取权限标识
     *
     * @param $resource
     * @return string
     */
    protected function getPermission($resource): string
    {
        $route = Route::getCurrentRoute();
        /**
         * 资源路由处理
         * 用于 create 与 update使用同一验证规则
         */
        if ($resource) {
            return str_replace(['@store', '@edit', '@show', '@destroys'], [
                '@create', '@update', '@index', '@destroy'
            ], $route->action['controller']);
        }
        return $route->action['controller'];
    }

    private function getUrlPermission($request)
    {
        return $request->path();
    }

    protected function hasUrlPermission(string $permission, string $guard): bool
    {
        $where = [
            ['name', '<=>', "$permission"],
            ['guard_name', '=', $guard],
        ];
        $has = DB::table(config('permission.table_names.permissions', 'permissions'))->where($where)->first();
        return boolval($has);
    }

    protected function getNamespacePermission(): string
    {
        $route = Route::getCurrentRoute();
        return $route->action['namespace'];
    }

}
