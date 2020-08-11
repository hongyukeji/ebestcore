<?php

namespace System\Listeners\Orders;

use System\Models\Order;
use Illuminate\Support\Facades\Log;
use System\Events\Orders\OrderStockCountEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use System\Models\Product;
use System\Models\ProductSku;

class OrderStockCountListener
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
     * @param OrderStockCountEvent $event
     * @return void
     */
    public function handle(OrderStockCountEvent $event)
    {
        $order = $event->order;

        foreach ($order->details as $item) {
            $product_id = $item->product_id;
            $product_sku_id = $item->product_sku_id;
            $product = Product::query()->findOrFail($product_id);
            if (empty($order->stock_count_at)) {
                // 根据商品库存计数方式操作,判断是否减少商品库存
                // 付款减库存 && 订单已付款
                if (empty($product->stock_count_mode == Product::STOCK_COUNT_MODE_PAYMENT) && $order->status_paid) {
                    if ($product_sku = ProductSku::query()->find($product_sku_id)) {
                        $product_sku->decrement('stock', $item->number);
                    } else {
                        $product->decrement('stock', $item->number);
                    }
                    $item->update([
                        'stock_count_at' => now(),
                    ]);
                } else if ($product->stock_count_mode == Product::STOCK_COUNT_MODE_PLACE_ORDER) {
                    if ($product_sku = ProductSku::query()->find($product_sku_id)) {
                        $product_sku->decrement('stock', $item->number);
                    } else {
                        $product->decrement('stock', $item->number);
                    }
                    $item->update([
                        'stock_count_at' => now(),
                    ]);
                }
            }
        }

    }
}
