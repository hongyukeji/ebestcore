<?php

namespace System\Listeners\Products;

use System\Models\Product;
use Illuminate\Support\Facades\Cache;
use System\Events\Products\CreateProductEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateProductListener
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
     * @param CreateProductEvent $event
     * @return void
     */
    public function handle(CreateProductEvent $event)
    {
        //$product = $event->product;

    }
}
