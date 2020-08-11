<?php

namespace System\Listeners\Plugins;

use System\Events\Plugins\PluginMiddlewareEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PluginMiddlewareListener
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
     * @param PluginMiddlewareEvent $event
     * @return void
     */
    public function handle(PluginMiddlewareEvent $event)
    {
        //
    }
}
