<?php

namespace System\Listeners\Systems;

use Illuminate\Support\Facades\Artisan;
use System\Events\Systems\InitDataEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitDataListener
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
     * @param InitDataEvent $event
     * @return void
     */
    public function handle(InitDataEvent $event)
    {
        //
    }
}
