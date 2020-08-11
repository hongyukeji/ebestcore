<?php

namespace System\Listeners\Orders;

use System\Models\Order;
use Illuminate\Support\Facades\Log;
use System\Events\Orders\OrderFinishedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderFinishedListener
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
     * @param OrderFinishedEvent $event
     * @return void
     */
    public function handle(OrderFinishedEvent $event)
    {
        $order = $event->order;

    }
}
