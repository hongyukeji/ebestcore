<?php

namespace System\Listeners\Plugins;

use System\Events\Plugins\PluginEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PluginListener
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
     * @param PluginEvent $event
     * @return void
     */
    public function handle(PluginEvent $event)
    {
        //
    }
}
