<?php

namespace System\Listeners\Systems;

use Illuminate\Support\Facades\Artisan;
use System\Events\Systems\UpdateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateListener
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
     * @param UpdateEvent $event
     * @return void
     */
    public function handle(UpdateEvent $event)
    {
        // Composer 命令
        \Illuminate\Support\Facades\Artisan::call('composer:autoload');
        \Illuminate\Support\Facades\Artisan::call('migrate', ["--force" => true]);
    }
}
