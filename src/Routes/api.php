<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if (config('systems.routes.api.status', true)) {
    // 默认Api接口
    Route::group(['namespace' => 'Api', 'prefix' => config('systems.routes.api.prefix', 'api'), 'as' => 'api.'], function () {
        // 首页
        Route::get('/', 'IndexController@index')->name('index');
    });

    // Api V1
    Route::group(['namespace' => 'Api', 'prefix' => config('systems.routes.api.prefix', 'api'), 'as' => 'api.'], function () {
        Route::group(['namespace' => 'V1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
            // 系统自动更新
            Route::post('system/auto-update', 'System\AutoUpdateController@index')->name('system.auto-update.index');
            // 支付
            Route::group(['namespace' => 'Payment', 'prefix' => 'payments', 'as' => 'payments.'], function () {
                Route::get('return/{gateway}', 'ReturnController@index')->name('return.index');
                Route::any('notify/{gateway}', 'NotifyController@index')->name('notify.index');
                Route::get('result', 'ResultController@index')->name('result.index');
            });
            // 图片上传
            Route::group(['namespace' => 'Uploads', 'prefix' => 'uploads', 'as' => 'uploads.'], function () {
                Route::resource('image', 'ImageController', ['only' => ['store']]);
                Route::post('ckeditor/image', 'CkEditorController@image')->name('ckeditor.image');
                Route::resource('ckeditor', 'CkEditorController', ['only' => ['index', 'store']]);
            });
            Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth'], function () {
                // 登录
                Route::post('login', 'LoginController@login')->name('.login');
                Route::post('register/mobile', 'RegisterController@mobile')->name('.register.mobile');
                // 手机号快速注册
                Route::post('register/mobile', 'RegisterController@mobile')->name('.register.mobile');
                // 发送注册短信验证码
                Route::post('register/send-verify-code', 'RegisterController@sendVerifyCode')->name('.register.send-verify-code');
                // 注册会员
                Route::post('register', 'RegisterController@store')->name('.register.store');
                Route::group(['middleware' => 'auth.jwt'], function () {
                    // 退出
                    Route::post('logout', 'LoginController@logout')->name('.logout');
                });
            });
            // 商品
            Route::resource('products', 'ProductsController');
        });
    });

    $api = app('Dingo\Api\Routing\Router');
    $api->version('v1', function ($api) {
        $api->group(['namespace' => 'System\Http\Controllers\Api\V1'], function ($api) {
            $api->group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth'], function ($api) {
                // 登录
                $api->post('login', 'LoginController@login')->name('.login');
                $api->post('register/mobile', 'RegisterController@mobile')->name('.register.mobile');
                // 手机号快速注册
                $api->post('register/mobile', 'RegisterController@mobile')->name('.register.mobile');
                // 发送注册短信验证码
                $api->post('register/send-verify-code', 'RegisterController@sendVerifyCode')->name('.register.send-verify-code');
                // 注册会员
                $api->post('register', 'RegisterController@store')->name('.register.store');
                $api->group(['middleware' => 'auth.jwt'], function ($api) {
                    // 退出
                    $api->post('logout', 'LoginController@logout')->name('.logout');
                });
            });
            // 页面数据
            $api->group(['namespace' => 'Page', 'prefix' => 'page', 'as' => 'page'], function ($api) {
                $api->get('site', 'SiteController@index')->name('.site.index');
                $api->get('index', 'IndexController@index')->name('.index.index');
                $api->get('guide', 'GuideController@index')->name('.guide.index');
                $api->get('user', 'UserController@index')->name('.user.index');
            });
            // 分类
            $api->get('categories/tree', 'CategoriesController@tree')->name('categories.tree');
            $api->resource('categories', 'CategoriesController');
            // 品牌
            $api->resource('brands', 'BrandsController');
            // 店铺
            $api->resource('shops', 'ShopsController');
            // 商品
            $api->resource('products', 'ProductsController');
            // 支付
            $api->post('payments/order', 'PaymentController@order')->name('payments.order');
            $api->resource('payments', 'PaymentController');
            // 权限组
            $api->group(['middleware' => ['api.auth']], function ($api) {
                // 购物车
                $api->resource('carts', 'CartsController');
                // 订单
                $api->resource('orders', 'OrderController');
                // 支付日志
                $api->resource('payment-logs', 'PaymentLogController');
                // 会员路由组
                $api->group(['namespace' => 'User', 'prefix' => 'user', 'as' => 'user',], function ($api) {
                    // 会员首页
                    $api->get('/', 'UserController@index')->name('.index');
                    // 收货地址
                    $api->resource('addresses', 'AddressController');
                    // 我的订单
                    $api->post('orders/confirm-received', 'OrderController@confirmReceived')->name('.orders.confirm-received');
                    $api->post('orders/payment', 'OrderController@payment')->name('.orders.payment');
                    $api->resource('orders', 'OrderController');
                });

            });
        });

        /*$api->group(['middleware' => 'foo'], function ($api) {
            // Endpoints registered here will have the "foo" middleware applied.
        });*/
    });
}
