<?php

use Illuminate\Support\Facades\Route;

if (config('systems.routes.frontend.status', true)) {
    Route::group([
        'namespace' => 'Frontend',
        'prefix' => config('systems.routes.frontend.prefix', 'frontend'),
        'as' => 'frontend.',
        //'middleware' => [],
        'domain' => config('systems.routes.frontend.domain', '')
    ], function () {
        Route::get('/', 'IndexController@index')->name('index');
        // 多语言切换
        /*Route::get('/language/{locale}', function ($locale) {
            $locales = get_app_locales();
            if (@in_array($locale, $locales)) {
                $cookie = cookie('language', $locale);
            } else {
                $cookie = cookie('language', config('app.locale'));
            }
            return response()->redirectTo(url()->previous())->cookie($cookie);
        })->name('language');*/
        // 权限认证
        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {
            // 发送注册短信验证码
            Route::post('register/send-verify-code', 'RegisterController@sendVerifyCode')->name('register.send-verify-code');

            // 短信验证码登录
            Route::get('login/verify-code', 'LoginController@showVerifyCodeForm')->name('verify-code');
            Route::post('login/verify-code', 'LoginController@verifyCode');
            Route::post('login/verify-code/send-verify-code', 'LoginController@sendVerifyCode')->name('verify-code.send-verify-code');

            // 用户身份验证相关的路由
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout')->name('logout');

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
        Route::group(['middleware' => ['auth.user:web,frontend']], function () {
            // 购物车
            Route::resource('carts', 'CartController');
            Route::resource('orders', 'OrderController');
        });
        // 支付
        Route::any('payments/handle', 'PaymentController@handle')->name('payments.handle');
        Route::get('payments/result', 'PaymentController@result')->name('payments.result');
        Route::get('payments', 'PaymentController@index')->name('payments.index');

        // 会员
        Route::group(['namespace' => 'User', 'prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth.user:web,frontend']], function () {
            Route::get('/', 'IndexController@index')->name('index');
            // 收货地址
            Route::resource('address', 'AddressController');
            Route::resource('invoices', 'InvoicesController');
            // 订单
            Route::any('orders/confirm-received/{order}', 'OrderController@confirmReceived')->name('orders.confirm-received');
            Route::any('orders/cancel/{order}', 'OrderController@cancelOrder')->name('orders.cancel');
            Route::any('orders/payment/{order}', 'OrderController@payment')->name('orders.payment');
            Route::resource('orders', 'OrderController');
            // 账户 - 提现申请
            Route::post('accounts/cash-withdrawal', 'AccountController@cashWithdrawal')->name('accounts.cash-withdrawal');
            // 账户 - 余额充值
            Route::post('accounts/recharge-balance', 'AccountController@rechargeBalance')->name('accounts.recharge-balance');
            // 账户
            Route::resource('accounts', 'AccountController');
            // 卡片
            Route::resource('cards', 'CardsController');
            // 提现账户
            Route::resource('cash-withdrawal-accounts', 'CashWithdrawalAccountController');
            // 个人资料
            Route::resource('profiles', 'ProfileController');
            // 收藏
            Route::resource('favorites', 'FavoriteController');
            Route::group(['namespace' => 'Favorite', 'prefix' => 'favorite', 'as' => 'favorite.'], function () {
                // 收藏商品
                Route::resource('products', 'ProductController');
                // 收藏店铺
                Route::resource('shops', 'ShopController');
            });
            // 浏览
            Route::resource('browses', 'BrowseController');
            Route::group(['namespace' => 'Browse', 'prefix' => 'browse', 'as' => 'browse.'], function () {
                // 收藏商品
                Route::resource('products', 'ProductController');
                // 收藏店铺
                Route::resource('shops', 'ShopController');
            });
        });
        // 店铺
        Route::resource('shops', 'ShopController');
        Route::get('chat/index', 'ChatController@index')->name('chat.index');

        // 安装
        Route::group(['prefix' => 'install', 'as' => 'install.'], function () {
            Route::get('/', 'InstallController@index')->name('index');
            Route::get('requirements', 'InstallController@requirements')->name('requirements');
            Route::get('database', 'InstallController@database')->name('database');
            Route::post('install', 'InstallController@install')->name('install');
            Route::get('setting', 'InstallController@settingForm')->name('setting');
            Route::post('setting', 'InstallController@setting')->name('setting');
            Route::get('finish', 'InstallController@finish')->name('finish');
        });
    });
}
