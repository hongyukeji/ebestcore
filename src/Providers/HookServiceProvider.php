<?php

namespace System\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use TorMorten\Eventy\Facades\Eventy;

/**
 * Class HookServiceProvider
 * @package System\Providers
 * @see https://github.com/tormjens/eventy
 */
class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1.预设钩子
        //\TorMorten\Eventy\Facades\Eventy::action('my.hook', 'awesome');

        // 2.定义钩子触发函数
        /*\TorMorten\Eventy\Facades\Eventy::addAction('my.hook', function($what) {
            echo 'You are '. $what;
        }, 20, 1);*/
    }
}
