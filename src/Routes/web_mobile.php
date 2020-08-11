<?php

use Illuminate\Support\Facades\Route;

if (config('systems.routes.mobile.status', true)) {
    Route::group([
        'namespace' => 'Mobile',
        'prefix' => config('systems.routes.mobile.prefix', 'mobile'),
        'as' => 'mobile.',
        //'middleware' => [],
        'domain' => config('systems.routes.mobile.domain', '')
    ], function () {
        Route::get('/', 'IndexController@index')->name('index');

        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {
            // 用户身份验证相关的路由
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout')->name('logout');

            // 短信验证码登录
            Route::get('login/verify-code', 'LoginController@showVerifyCodeForm')->name('verify-code');
            Route::post('login/verify-code', 'LoginController@verifyCode');
            Route::post('login/verify-code/send-verify-code', 'LoginController@sendVerifyCode')->name('verify-code.send-verify-code');

            // 用户注册相关路由
            Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
            Route::post('register', 'RegisterController@register');

            // 密码重置相关路由
            Route::get('forget/password', 'ForgotPasswordController@showLinkRequestForm')->name('forget.password');
            Route::post('forget/verify-code', 'ForgotPasswordController@sendVerifyCode')->name('forget.verify-code');
            Route::get('forget/verify', 'ForgotPasswordController@showVerifyForm')->name('forget.verify');
            Route::post('forget/verify', 'ForgotPasswordController@verify');

            //Route::get('forget/reset', 'ResetPasswordController@reset')->name('forget.reset');
            //Route::post('forget/reset-password', 'ResetPasswordController@reset')->name('forget.reset-password');
            Route::post('forget/reset-mobile', 'ResetPasswordController@resetMobile')->name('forget.reset-mobile');

            Route::get('password/reset', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

            // Email 认证相关路由
            Route::get('verification/email/verify', 'Verification\EmailController@show')->name('verification.email.notice');
            Route::get('verification/email/verify/{id}/{hash}', 'Verification\EmailController@verify')->name('verification.email.verify');
            Route::get('verification/email/resend', 'Verification\EmailController@resend')->name('verification.email.resend');

            // Mobile 认证相关路由
            Route::get('verification/mobile/verify', 'Verification\MobileController@show')->name('verification.mobile.notice');
            Route::get('verification/mobile/verify/{id}', 'Verification\MobileController@verify')->name('verification.mobile.verify');
            Route::get('verification/mobile/resend', 'Verification\MobileController@resend')->name('verification.mobile.resend');
        });
        // 页面
        Route::resource('pages', 'PagesController');
        // 文章
        Route::resource('articles', 'ArticleController');
        // 商品
        Route::resource('products', 'ProductController');
        // 商品分类
        Route::resource('category', 'CategoryController');
        // 购物车
        Route::resource('carts', 'CartController');
        // 搜索
        Route::resource('search', 'SearchController');
        // 订单
        Route::resource('orders', 'OrderController');
        // 支付
        Route::any('payments/handle', 'PaymentController@handle')->name('payments.handle');
        Route::get('payments/result', 'PaymentController@result')->name('payments.result');
        Route::resource('payments', 'PaymentController');
        // 用户
        Route::group(['namespace' => 'User', 'prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth.user:web,mobile']], function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::get('/{id}/edit', 'UserController@edit')->name('edit');
            // 收货地址
            Route::resource('address', 'AddressController');
            // 订单
            Route::any('orders/confirm-received/{order}', 'OrderController@confirmReceived')->name('orders.confirm-received');
            Route::any('orders/cancel/{order}', 'OrderController@cancelOrder')->name('orders.cancel');
            Route::any('orders/payment/{order}', 'OrderController@payment')->name('orders.payment');
            Route::resource('orders', 'OrderController');
            // 账户
            Route::resource('accounts', 'AccountController');
            // 个人资料
            Route::resource('profiles', 'ProfileController');
        });
    });
}
