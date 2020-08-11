<?php

namespace System\Listeners\Menus;

use System\Events\Menus\SyncMenuEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncMenuListener
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
     * @param SyncMenuEvent $event
     * @return void
     */
    public function handle(SyncMenuEvent $event)
    {
        //
    }
}
