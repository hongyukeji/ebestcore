<?php

if (config('systems.routes.seller.status', true)) {
    Route::group([
        'namespace' => 'Seller',
        'prefix' => config('systems.routes.seller.prefix', 'seller'),
        'as' => 'seller.',
        'domain' => config('systems.routes.seller.domain', '')
    ], function () {

        // 权限认证
        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {
            // 用户身份验证相关的路由
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout')->name('logout');
        });

        // 已登录
        Route::middleware(['auth.user:web,seller'])->group(function () {

            // 申请入驻
            Route::get('apply/shop', 'ApplyController@shop')->name('apply.shop');
            Route::get('apply/status', 'ApplyController@status')->name('apply.status');
            Route::resource('apply', 'ApplyController');

            Route::middleware(['permission.seller'])->group(function () {

                // 首页
                Route::get('/', 'IndexController@index')->name('index');
                // 商品
                Route::resource('products', 'ProductsController');
                // 订单
                Route::resource('orders', 'OrdersController');

                // 设置
                Route::namespace('Settings')->prefix('settings')->name('settings.')->group(function () {
                    // 基本设置
                    Route::resource('bases', 'BasesController', ['only' => ['index', 'store']]);
                });

            });

        });
    });
}
