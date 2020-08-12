<?php

return [

    'default_gateway' => 'balance',

    'default_options' => [
        'test_mode' => true,
        // ...
    ],
    'support_terminals' => [
        'pc' => '电脑端',
        'wap' => '手机端',
        'app' => 'App端',
        'miniapp' => '小程序端',
    ],
    'gateways' => [
        'cash' => [
            'name' => '货到付款',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\Cash',
            'type' => 'local',
            'options' => [],
            'support_terminal' => [
                'pc', 'wap', 'app', 'miniapp',
            ],
            'status' => false,
        ],
        'balance' => [
            'name' => '余额支付',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\Balance',
            'type' => 'local',
            'options' => [],
            'support_terminal' => [
                'pc', 'wap', 'app', 'miniapp',
            ],
            'status' => true,
        ],
        'bankwire' => [
            'name' => '电子汇款',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\BankWire',
            'type' => 'local',
            'options' => [
                'payee_name' => '旺迈特科技有限公司', // 收款人名称
                'account_number' => '6222 0000 0000 0000 000', // 银行账号
                'opening_bank' => '中国工商银行北京支行', // 开户行
                'bank_name' => '工商银行', // 银行名称
            ],
            'support_terminal' => [
                'pc', 'wap', 'app', 'miniapp',
            ],
            'status' => false,
        ],
        /*'paypal' => [
            'name' => 'PayPal',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\PayPal',
            'type' => 'online',
            'options' => [
                'username' => env('PAYMENT_PAYPAL_USERNAME'),
                'password' => env('PAYMENT_PAYPAL_PASSWORD'),
                'signature' => env('PAYMENT_PAYPAL_SIGNATURE'),
                'test_mode' => env('PAYMENT_PAYPAL_TEST_MODE'),
            ],
            'status' => true,
        ],*/
        'alipay' => [
            'name' => '支付宝',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\AliPay',
            'type' => 'online',
            'options' => [
                'app_id' => env('PAYMENT_ALIPAY_APP_ID'),
                'ali_public_key' => env('PAYMENT_ALIPAY_ALI_PUBLIC_KEY'),
                'private_key' => env('PAYMENT_ALIPAY_PRIVATE_KEY'),
                'return_url' => env('PAYMENT_ALIPAY_RETURN_URL'),
                'notify_url' => env('PAYMENT_ALIPAY_NOTIFY_URL'),
            ],
            'support_terminal' => [
                'pc', 'wap', 'app', 'miniapp',
            ],
            'status' => true,
        ],
        'wechat' => [
            'name' => '微信支付',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\WeChat',
            'type' => 'online',
            'options' => [
                'app_id' => env('PAYMENT_WECHAT_APP_ID'),
                'app_secret' => env('PAYMENT_WECHAT_APP_SECRET'),
                'mch_id' => env('PAYMENT_WECHAT_MCH_ID'),
                'key' => env('PAYMENT_WECHAT_KEY'),
                'return_url' => env('PAYMENT_WECHAT_RETURN_URL'),
                'notify_url' => env('PAYMENT_WECHAT_NOTIFY_URL'),
            ],
            'support_terminal' => [
                'pc', 'wap', 'app', 'miniapp',
            ],
            'status' => true,
        ],
        'payjs' => [
            'name' => 'PayJs',
            'driver' => '\System\\Librarys\\Payment\\Gateways\\PayJs',
            'type' => 'online',
            'options' => [
                'mchid' => env('PAYMENT_PAYJS_MCHID'),
                'key' => env('PAYMENT_PAYJS_KEY'),
                'return_url' => env('PAYMENT_WECHAT_RETURN_URL'),
                'notify_url' => env('PAYMENT_WECHAT_NOTIFY_URL'),
                'max_payment_amount' => '1000',
                'daily_limit_amount' => '50000',
            ],
            'support_terminal' => [
                'pc', 'wap',
            ],
            'status' => false,
        ],
        // other gateways ...
    ],
];
