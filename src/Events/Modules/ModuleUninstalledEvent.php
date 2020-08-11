<?php

namespace System\Events\Modules;

use System\Events\Event;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleUninstalledEvent extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $module;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($module)
    {
        $this->module = $module;
    }
}