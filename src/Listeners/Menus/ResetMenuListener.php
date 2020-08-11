<?php

namespace System\Listeners\Menus;

use System\Events\Menus\ResetMenuEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetMenuListener
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
     * @param ResetMenuEvent $event
     * @return void
     */
    public function handle(ResetMenuEvent $event)
    {
        //
    }
}
