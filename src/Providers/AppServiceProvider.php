<?php

namespace System\Providers;

use System\Utils\Util\Providers\UtilServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Schema::defaultStringLength(191);

        /*if (!app()->isLocal()) {
            if (class_exists('Barryvdh\\Debugbar\\Console\\ClearCommand')) {
                \DebugBar::disable();
            }
        }*/

        $this->mergeConfigFrom(__DIR__ . '/../Config/app.php', 'app');
        $this->mergeConfigFrom(__DIR__ . '/../Config/api.php', 'api');
        $this->mergeConfigFrom(__DIR__ . '/../Config/broadcasting.php', 'broadcasting');
        $this->mergeConfigFrom(__DIR__ . '/../Config/captcha.php', 'captcha');
        $this->mergeConfigFrom(__DIR__ . '/../Config/debugbar.php', 'debugbar');
        $this->mergeConfigFrom(__DIR__ . '/../Config/demo.php', 'demo');
        $this->mergeConfigFrom(__DIR__ . '/../Config/elfinder.php', 'elfinder');
        $this->mergeConfigFrom(__DIR__ . '/../Config/empower.php', 'empower');
        $this->mergeConfigFrom(__DIR__ . '/../Config/geoip.php', 'geoip');
        $this->mergeConfigFrom(__DIR__ . '/../Config/image.php', 'image');
        $this->mergeConfigFrom(__DIR__ . '/../Config/imageup.php', 'imageup');
        $this->mergeConfigFrom(__DIR__ . '/../Config/install.php', 'install');
        $this->mergeConfigFrom(__DIR__ . '/../Config/jwt.php', 'jwt');
        $this->mergeConfigFrom(__DIR__ . '/../Config/log-viewer.php', 'log-viewer');
        $this->mergeConfigFrom(__DIR__ . '/../Config/menus.php', 'menus');
        $this->mergeConfigFrom(__DIR__ . '/../Config/modules.php', 'modules');
        $this->mergeConfigFrom(__DIR__ . '/../Config/oauth.php', 'oauth');
        $this->mergeConfigFrom(__DIR__ . '/../Config/params.php', 'params');
        $this->mergeConfigFrom(__DIR__ . '/../Config/payments.php', 'payments');
        $this->mergeConfigFrom(__DIR__ . '/../Config/permission.php', 'permission');
        $this->mergeConfigFrom(__DIR__ . '/../Config/pipeline.php', 'pipeline');
        $this->mergeConfigFrom(__DIR__ . '/../Config/plugins.php', 'plugins');
        $this->mergeConfigFrom(__DIR__ . '/../Config/purifier.php', 'purifier');
        $this->mergeConfigFrom(__DIR__ . '/../Config/rbac.php', 'rbac');
        $this->mergeConfigFrom(__DIR__ . '/../Config/settings.php', 'settings');
        $this->mergeConfigFrom(__DIR__ . '/../Config/shop.php', 'shop');
        $this->mergeConfigFrom(__DIR__ . '/../Config/sms.php', 'sms');
        $this->mergeConfigFrom(__DIR__ . '/../Config/statuscode.php', 'statuscode');
        $this->mergeConfigFrom(__DIR__ . '/../Config/systems.php', 'systems');
        $this->mergeConfigFrom(__DIR__ . '/../Config/telescope.php', 'telescope');
        $this->mergeConfigFrom(__DIR__ . '/../Config/terminal.php', 'terminal');
        $this->mergeConfigFrom(__DIR__ . '/../Config/themes.php', 'themes');
        $this->mergeConfigFrom(__DIR__ . '/../Config/translate.php', 'translate');
        $this->mergeConfigFrom(__DIR__ . '/../Config/ueditor.php', 'ueditor');
        $this->mergeConfigFrom(__DIR__ . '/../Config/uploads.php', 'uploads');
        $this->mergeConfigFrom(__DIR__ . '/../Config/validation.php', 'validation');
        $this->mergeConfigFrom(__DIR__ . '/../Config/websites.php', 'websites');


        $this->mergeConfigFrom(__DIR__ . '/../Config/wmt.php', 'wmt');


        // 手机号验证规则
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            $regex = config('params.regex.mobile', '/^1\d{10}$/');
            return preg_match($regex, $value);
        });

        // 用户名禁止出现关键词规则
        Validator::extend('censor_username', function ($attribute, $value, $parameters, $validator) {
            $censor_names = explode(',', config('params.register.censor_names'));
            return Str::contains($value, $censor_names) ? false : true;
        });

        // 用户名验证规则：用户名只能由数字、字母、中文汉字及下划线组成，不能包含特殊符号。
        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            $regex = config('params.regex.username', '/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u');
            return preg_match($regex, $value);
        });

        // 价格验证规则
        Validator::extend('price', function ($attribute, $value, $parameters, $validator) {
            $regex = config('params.regex.price', '/^(?!0\d|[0.]+$)\d{1,8}(\.\d{1,2})?$/');
            return preg_match($regex, $value);
        });

        // 添加集合分页支持,使用: $items->paginate(10);
        if (!Collection::hasMacro('paginate')) {
            Collection::macro('paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    return (new LengthAwarePaginator(
                        $this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))
                        ->withPath('');
                });
        }

        // 创建是sms短信频道
        $this->app->make(\Illuminate\Notifications\ChannelManager::class)
            ->extend('sms', function ($app) {
                return $app->make(\System\Channels\SmsChannel::class);
            });

        /*
        // 绑定处理 HTTP 请求的接口实现到服务容器
        $this->app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \System\Http\Kernel::class
        );

        // 绑定处理 Console 请求的接口实现到服务容器
        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \System\Console\Kernel::class
        );
        */
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Hongyukeji\LaravelSettings\Providers\SettingsServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(BroadcastServiceProvider::class);
        $this->app->register(HookServiceProvider::class);  // Hook 钩子
        $this->app->register(RepositoryServiceProvider::class);  // Repositories 仓库模式
        $this->app->register(UtilServiceProvider::class);  // Util 工具类
        $this->app->register(ObserverServiceProvider::class);  // 模型观察者
        $this->app->register(CommandServiceProvider::class);  // 命令
        if (class_exists('Laravel\Telescope\Telescope') && class_exists('Laravel\Telescope\TelescopeApplicationServiceProvider')) {
            $this->app->register(\System\Providers\TelescopeServiceProvider::class);  // Telescope 望远镜
        }
    }
}
