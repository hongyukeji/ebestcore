<?php

namespace System\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 例子
        $this->app->bind(
            \System\Repository\Interfaces\ExampleInterface::class,
            \System\Repository\Repositories\ExampleRepository::class
        );
        // 用户
        $this->app->bind(
            \System\Repository\Interfaces\UserInterface::class,
            \System\Repository\Repositories\UserRepository::class
        );
        // 订单
        $this->app->bind(
            \System\Repository\Interfaces\OrderInterface::class,
            \System\Repository\Repositories\OrderRepository::class
        );
        // 分类
        $this->app->bind(
            \System\Repository\Interfaces\CategoryInterface::class,
            \System\Repository\Repositories\CategoryRepository::class
        );
        // 品牌
        $this->app->bind(
            \System\Repository\Interfaces\BrandInterface::class,
            \System\Repository\Repositories\BrandRepository::class
        );
        // 商品
        $this->app->bind(
            \System\Repository\Interfaces\ProductInterface::class,
            \System\Repository\Repositories\ProductRepository::class
        );
        // 店铺
        $this->app->bind(
            \System\Repository\Interfaces\ShopInterface::class,
            \System\Repository\Repositories\ShopRepository::class
        );
        // 访客
        $this->app->bind(
            \System\Repository\Interfaces\VisitorInterface::class,
            \System\Repository\Repositories\VisitorRepository::class
        );
        // 菜单
        $this->app->bind(
            \System\Repository\Interfaces\MenuInterface::class,
            \System\Repository\Repositories\MenuRepository::class
        );
        // 地区
        $this->app->bind(
            \System\Repository\Interfaces\RegionInterface::class,
            \System\Repository\Repositories\RegionRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
