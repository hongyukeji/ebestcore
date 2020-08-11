<?php

if (config('systems.routes.helper.status', true)) {
    Route::group([
        'namespace' => 'Helper',
        'prefix' => config('systems.routes.helper.prefix', 'helper'),
        'as' => 'helper.',
        'domain' => config('systems.routes.helper.domain', '')
    ], function () {
        // 地图
        Route::group(['namespace' => 'Maps', 'prefix' => 'maps', 'as' => 'maps.',], function () {
            // 高德地图
            Route::resource('amap', 'AMapController', ['only' => ['index', 'show']]);
        });
        // 二维码
        Route::get('qr-code', 'QrCodeController@index')->name('qr-code.index');
        // 短网址
        Route::get('to/{code}', 'ShortUrlController@index')->name('short-url.index');
        // 打印 - 订单
        Route::get('prints/order', 'PrintController@order')->name('prints.order');
    });
}
