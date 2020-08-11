<?php

namespace System\Providers;

use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \System\Models\Admin::observe(\System\Observers\AdminObserver::class);
        \System\Models\User::observe(\System\Observers\UserObserver::class);
        \System\Models\UserAccount::observe(\System\Observers\UserAccountObserver::class);
        \System\Models\UserAddress::observe(\System\Observers\UserAddressObserver::class);
        \System\Models\OrderBalance::observe(\System\Observers\OrderBalanceObserver::class);
        \System\Models\Order::observe(\System\Observers\OrderObserver::class);
        \System\Models\ArticleCategory::observe(\System\Observers\ArticleCategoryObserver::class);
        \System\Models\Article::observe(\System\Observers\ArticleObserver::class);
        \System\Models\Category::observe(\System\Observers\CategoryObserver::class);
        \System\Models\Cart::observe(\System\Observers\CartObserver::class);
        \System\Models\Brand::observe(\System\Observers\BrandObserver::class);
        \System\Models\Product::observe(\System\Observers\ProductObserver::class);
        \System\Models\ProductSku::observe(\System\Observers\ProductSkuObserver::class);
        \System\Models\ProductExtend::observe(\System\Observers\ProductExtendObserver::class);
        \System\Models\ProductImage::observe(\System\Observers\ProductImageObserver::class);
        \System\Models\Region::observe(\System\Observers\RegionObserver::class);
        \System\Models\Navigation::observe(\System\Observers\NavigationObserver::class);
        \System\Models\PaymentLog::observe(\System\Observers\PaymentLogObserver::class);
    }
}
