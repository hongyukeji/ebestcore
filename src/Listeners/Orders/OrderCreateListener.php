<?php

namespace System\Listeners\Orders;

use System\Events\Orders\OrderCreateEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreateListener
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
     * @param OrderCreateEvent $event
     * @return void
     */
    public function handle(OrderCreateEvent $event)
    {
        $order = $event->order;

        // 订单库存盘点事件
        event(new \System\Events\Orders\OrderStockCountEvent($order));
    }
}
