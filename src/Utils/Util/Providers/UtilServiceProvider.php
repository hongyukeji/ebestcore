<?php

namespace System\Utils\Util\Providers;

use System\Utils\Util\Util;
use Illuminate\Support\ServiceProvider;

class UtilServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('util', function () {
            return new Util();
        });

        $this->app->alias(Util::class, 'util');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
