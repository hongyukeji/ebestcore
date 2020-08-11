<?php

namespace System\Listeners\Orders;

use System\Events\Orders\OrderPaidEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use System\Models\Product;
use System\Models\ProductSku;

class OrderPaidListener
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
     * @param OrderPaidEvent $event
     * @return void
     */
    public function handle(OrderPaidEvent $event)
    {
        $order = $event->order;

        // 商品销量
        foreach ($order->items as $item) {
            $product_sku = ProductSku::query()->find($item->product_sku_id);
            if ($product_sku) {
                if ($product_sku->sale_count <= 0) {
                    $product_sku->update(['sale_count' => 0]);
                }
                $product_sku->increment('sale_count', $item->number);
            }

            $product = Product::query()->find($item->product_id);
            if ($product) {
                if ($product->sale_count <= 0) {
                    $product->update(['sale_count' => 0]);
                }
                $product->increment('sale_count', $item->number);
            }
        }

        // 订单库存盘点事件
        event(new \System\Events\Orders\OrderStockCountEvent($order));
    }
}
