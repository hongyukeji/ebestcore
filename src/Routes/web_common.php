<?php

if (config('systems.routes.common.status', true)) {
    Route::group([
        'namespace' => 'Common',
        'prefix' => config('systems.routes.common.prefix', 'common'),
        'as' => 'common.',
        'domain' => config('systems.routes.common.domain', '')
    ], function () {
        Route::get('/', 'IndexController@index')->name('index');

        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function () {
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
        });
    });
}
