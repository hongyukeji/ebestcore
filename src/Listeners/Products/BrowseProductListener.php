<?php

namespace System\Listeners\Products;

use System\Models\Product;
use Illuminate\Support\Facades\Cache;
use System\Events\Products\BrowseProductEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BrowseProductListener
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
     * @param BrowseProductEvent $event
     * @return void
     */
    public function handle(BrowseProductEvent $event)
    {
        $product = $event->product;
        $session = request()->session()->getId() ?: uuid();
        $cache_prefix = 'browse_product_';
        $cache_key = $cache_prefix . $session . $product->id;

        // 缓存不存在则商品浏览次数+1，并缓存该商品键值，缓存有效期为12小时
        if (!Cache::has($cache_key)) {
            $product->update(['browse_count' => (int)$product->browse_count + 1]);
            Cache::put($cache_key, $product->id, now()->addHours(12));
        }
    }
}
