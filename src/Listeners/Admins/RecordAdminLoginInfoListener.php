<?php

namespace System\Listeners\Admins;

use System\Events\Admins\AdminLogin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordAdminLoginInfoListener
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
     * @param AdminLogin $event
     * @return void
     */
    public function handle(AdminLogin $event)
    {
        //
    }
}
