<?php

namespace System\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // 例子事件
        'System\Events\Examples\ExampleEvent' => [
            'System\Listeners\Examples\ExampleListener',
        ],

        // 系统安装 - 安装事件
        'System\Events\Systems\InstallEvent' => [
            'System\Listeners\Systems\InstallListener',
        ],

        // 系统 - 更新事件
        'System\Events\Systems\UpdateEvent' => [
            'System\Listeners\Systems\UpdateListener',
        ],

        // 系统 - 缓存事件
        'System\Events\Systems\CacheEvent' => [
            'System\Listeners\Systems\CacheListener',
        ],

        // 系统 - 初始化数据
        'System\Events\Systems\InitDataEvent' => [
            'System\Listeners\Systems\InitDataListener',
        ],

        // 模块 - 启用事件
        'System\Events\Modules\ModuleEnabledEvent' => [
            'System\Listeners\Modules\ModuleEnabledListener',
        ],
        // 模块 - 关闭事件
        'System\Events\Modules\ModuleDisabledEvent' => [
            'System\Listeners\Modules\ModuleDisabledListener',
        ],
        // 模块 - 卸载事件
        'System\Events\Modules\ModuleUninstalledEvent' => [
            'System\Listeners\Modules\ModuleUninstalledListener',
        ],

        // 菜单 - 恢复事件
        'System\Events\Menus\ResetMenuEvent' => [
            'System\Listeners\Menus\ResetMenuListener',
        ],
        // 菜单 - 同步事件
        'System\Events\Menus\SyncMenuEvent' => [
            'System\Listeners\Menus\SyncMenuListener',
        ],

        // 会员登录
        'System\Events\Users\UserLogin' => [
            // 记录会员登录信息
            'System\Listeners\Users\RecordUserLoginInfoListener',
        ],
        // 会员注册
        'System\Events\Users\UserRegister' => [
            // 记录会员注册信息
            'System\Listeners\Users\RecordUserRegisterInfoListener',
        ],

        // 管理员登录
        'System\Events\Admins\AdminLogin' => [
            // 记录会员登录信息
            'System\Listeners\Admins\RecordAdminLoginInfoListener',
        ],
        // 管理员注册
        'System\Events\Admins\AdminRegister' => [
            // 记录会员注册信息
            'System\Listeners\Admins\RecordAdminRegisterInfoListener',
        ],

        // 订单创建
        'System\Events\Orders\OrderCreateEvent' => [
            'System\Listeners\Orders\OrderCreateListener',
        ],
        // 订单已付款
        'System\Events\Orders\OrderPaidEvent' => [
            'System\Listeners\Orders\OrderPaidListener',
        ],
        // 订单库存盘点
        'System\Events\Orders\OrderStockCountEvent' => [
            'System\Listeners\Orders\OrderStockCountListener',
        ],
        // 订单已发货
        'System\Events\Orders\OrderShippedEvent' => [
            'System\Listeners\Orders\SendShipmentNotificationListener',
        ],
        // 订单已完成事件
        'System\Events\Orders\OrderFinishedEvent' => [
            'System\Listeners\Orders\OrderFinishedListener',
        ],
        // 订单结算事件
        'System\Events\Orders\OrderSettlementEvent' => [
            'System\Listeners\Orders\OrderSettlementListener',
        ],

        // 创建商品
        'System\Events\Products\CreateProductEvent' => [
            'System\Listeners\Products\CreateProductListener',
        ],

        // 浏览商品
        'System\Events\Products\BrowseProductEvent' => [
            'System\Listeners\Products\BrowseProductListener',
        ],

        // 浏览文章
        'System\Events\Articles\BrowseArticleEvent' => [
            'System\Listeners\Articles\BrowseArticleListener',
        ],

        // 插件事件
        'System\Events\Plugins\PluginEvent' => [
            'System\Listeners\Plugins\PluginListener',
        ],
        // 插件 - 中间件事件
        'System\Events\Plugins\PluginMiddlewareEvent' => [
            'System\Listeners\Plugins\PluginMiddlewareListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
