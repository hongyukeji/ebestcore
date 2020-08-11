<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ThemeMiddleware
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
        try {
            $themes = config('themes.templates');
            $view_path = str_finish(config('themes.view_path', resource_path('views')), DIRECTORY_SEPARATOR);
            foreach ($themes as $key => $theme) {
                $path_prefix = str_finish($theme['path_prefix'], DIRECTORY_SEPARATOR);
                $template_default = "{$view_path}{$path_prefix}{$theme['template_default']}";
                $template = "{$view_path}{$path_prefix}{$theme['template']}";
                // 默认主题模板
                View::addNamespace($key, $template_default);
                // 当前主题模板
                View::prependNamespace($key, $template);
            }
        } catch (\Exception $e) {
            logger('[ThemeMiddleware]' . $e->getMessage());
        }

        return $next($request);
    }
}
