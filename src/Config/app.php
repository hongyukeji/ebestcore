<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 系统开发
    |--------------------------------------------------------------------------
    |
    | 开发者信息
    |
    */

    'system' => env('APP_SYSTEM', 'eBestMall'),

    'system_name' => env('APP_SYSTEM_NAME', '旺迈特'),

    'version' => '3.9.107',

    'author' => env('APP_AUTHOR', '鸿宇科技'),

    'support' => env('APP_SUPPORT', 'http://wmt.ltd'),


    /*
    |--------------------------------------------------------------------------
    | Update Source
    |--------------------------------------------------------------------------
    |
    | Where to get information of new versions.
    |
    */
    //'update_source' => env('APP_UPDATE_SOURCE', 'http://wmt.ltd/cloud/update'),

    'update_notice_url' => env('APP_UPDATE_NOTICE_URL', ''),    // 更新Token

    'update_token' => env('APP_UPDATE_TOKEN', ''),    // 更新Token

    'update_stability' => env('APP_UPDATE_STABILITY', '1'),    // 更新稳定性:0,dev;1,stable

    'update_auto' => env('APP_UPDATE_AUTO', true),    // 自动更新


];
