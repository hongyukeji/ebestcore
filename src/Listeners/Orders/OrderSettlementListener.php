<?php

namespace System\Listeners\Orders;

use System\Models\Order;
use Illuminate\Support\Facades\Log;
use System\Events\Orders\OrderSettlementEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderSettlementListener
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
     * @param OrderSettlementEvent $event
     * @return void
     */
    public function handle(OrderSettlementEvent $event)
    {
        try {
            $order = $event->order;
            if (empty($order->settlement_at)) {
                // 结算金额
                $money = $order->total_amount;
                // 第三方店铺
                if ($order->shop->id > 0 && $order->shop->user_id > 0) {
                    $order->shop->user->account->increment('money', $money);
                }
                // 更新订单信息
                $order->update([
                    'settlement_at' => now(),
                    'finished_at' => now(),
                    'status' => Order::STATUS_FINISH,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("[" . get_class() . "]" . $e->getMessage());
        }
    }
}
