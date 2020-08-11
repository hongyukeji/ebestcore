<?php

namespace System\Listeners\Systems;

use Illuminate\Support\Facades\Artisan;
use System\Events\Systems\InstallEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstallListener
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
     * @param InstallEvent $event
     * @return void
     */
    public function handle(InstallEvent $event)
    {
        //
    }
}
