<?php

return [
    'github' => [
        'name' => 'GitHub',
        'client' => 'web',
        'client_id' => env('OAUTH_GITHUB_CLIENT_ID'),
        'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET'),
        'redirect' => env('OAUTH_GITHUB_REDIRECT_URI'),
        'status' => 0,
    ],

    'qq' => [
        'name' => 'QQ',
        'client' => '',
        'official_website' => 'https://connect.qq.com/manage.html',   // 官网
        'client_id' => env('OAUTH_QQ_KEY'),
        'client_secret' => env('OAUTH_QQ_SECRET'),
        'redirect' => env('OAUTH_QQ_REDIRECT_URI'),
        'status' => 0,
        // 以下为扩展信息...
        //'redirect_tip' => '网站回调域：QQ互联 => 应用管理 => 基本信息 => 平台信息 => 网站回调域',   // QQ互联设置提示
    ],

    'weixin' => [
        'name' => '微信',
        'client' => '',
        'client_id' => env('OAUTH_WEIXIN_KEY'),
        'client_secret' => env('OAUTH_WEIXIN_SECRET'),
        'redirect' => env('OAUTH_WEIXIN_REDIRECT_URI'),
        'status' => 0,
    ],

    'weixinweb' => [
        'name' => '微信电脑端',
        'client' => 'web',
        'client_id' => env('OAUTH_WEIXINWEB_KEY'),
        'client_secret' => env('OAUTH_WEIXINWEB_SECRET'),
        'redirect' => env('OAUTH_WEIXINWEB_REDIRECT_URI'),
        'status' => 0,
    ],

    'weibo' => [
        'name' => '微博',
        'client' => '',
        'client_id' => env('OAUTH_WEIBO_KEY'),
        'client_secret' => env('OAUTH_WEIBO_SECRET'),
        'redirect' => env('OAUTH_WEIBO_REDIRECT_URI'),
        'status' => 0,
    ],
];