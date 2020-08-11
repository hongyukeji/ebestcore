<?php

namespace System\Listeners\Admins;

use System\Events\Admins\AdminRegister;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordAdminRegisterInfoListener
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
     * @param AdminRegister $event
     * @return void
     */
    public function handle(AdminRegister $event)
    {
        //
    }
}
