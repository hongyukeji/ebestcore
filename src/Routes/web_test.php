<?php

if (app()->isLocal()) {
    Route::group(['namespace' => 'Test', 'prefix' => 'test', 'as' => 'test.',], function () {
        Route::get('/', 'IndexController@index')->name('index');
    });
}
