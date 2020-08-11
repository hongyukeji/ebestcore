<?php

namespace System\Listeners\Systems;

use Illuminate\Support\Facades\Artisan;
use System\Events\Systems\CacheEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CacheEvent $event
     * @return void
     */
    public function handle(CacheEvent $event)
    {
        // Composer 命令 - 清除缓存
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
    }
}
