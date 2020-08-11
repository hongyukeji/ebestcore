<?php

namespace System\Listeners\Orders;

use System\Events\Orders\OrderShippedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShipmentNotificationListener
{
    public function __construct()
    {
        //
    }

    public function handle(OrderShippedEvent $event)
    {
        $order = $event->order;
    }
}
