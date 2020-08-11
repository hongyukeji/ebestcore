<?php

namespace System\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Language
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
        // 判断 cookie 语言language字段是否存在
        $locale = $request->cookie('language');
        if (!empty($locale)) {
            if (in_array($locale, get_app_locales())) {
                // 系统语言设置为用户设置语言
                App::setLocale($locale);
            }
        }

        /*$locales = get_app_locales();
        if (!in_array($locale, $locales)) {
            // 浏览器语言是否在系统语言列表中,如果存在则设置为浏览器默认语言, 不在则设置为系统默认语言
            $browser_language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
            if (!empty($browser_language) && in_array($browser_language, $locales)) {
                App::setLocale($browser_language);
            } else {
                App::setLocale(Config::get('app.locale'));
            }
        }*/
        return $next($request);
    }
}
