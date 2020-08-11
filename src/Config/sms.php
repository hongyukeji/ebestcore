<?php

return [
    'driver' => env('SMS_DRIVER', 'aliyun'),

    'default' => [
        'gateway' => env('SMS_DEFAULT_GATEWAY', 'aliyun'),
    ],

    'config' => [
        'debug' => env('SMS_CONFIG_DEBUG', false),
        'default_sign' => '',
        'default_verify_code' => '1234',
        'verify_code' => [
            'limit_rule' => 'throttle:5',
            'expires_at' => 60 * 15,
        ],
        'templates' =>
            [
                [
                    'name' => '验证码',
                    'key' => 'verify_code',
                    'description' => '您的验证码是#code#。有效期为15分钟，请尽快验证！',
                ],
                [
                    'name' => '用户-付款提醒',
                    'key' => 'user_account_pay',
                    'description' => '已经收到您的订单，订单编号：#orderSn#，我们将尽快给您发货。',
                ],
                [
                    'name' => '用户-发货提醒',
                    'key' => 'user_order_deliver_goods',
                    'description' => '您的订单已发货，订单编号：#orderSn#，收货人：#consignee#，联系电话：#phone#，收货地址：#address#，请注意查收！',
                ],
                [
                    'name' => '商家-新订单提醒',
                    'key' => 'seller_new_order',
                    'description' => '您有一条新订单，订单编号：#orderSn#，请注意查看。',
                ],
                [
                    'name' => '商家-订单已付款提醒',
                    'key' => 'seller_order_account_pay',
                    'description' => '客户已付款，订单编号：#orderNumber#，收货人：#consignee#，联系电话：#phone#，订单金额：#money#。',
                ],
            ],
    ],

    'gateways' => [
        'aliyun' => [
            'sms_name' => '阿里云',
            'sms_url' => 'https://dysms.console.aliyun.com/dysms.htm',
            'accessKeyId' => env('SMS_ALIYUN_ACCESSKEYID'),
            'accessKeySecret' => env('SMS_ALIYUN_ACCESSKEYSECRET'),
            'signName' => env('SMS_ALIYUN_SIGNNAME'),
        ],
        'yunpian' => [
            'sms_name' => '云片网',
            'sms_url' => 'https://www.yunpian.com',
            'apikey' => env('SMS_YUNPIAN_APIKEY'),
        ],
        /*'qcloud' => [
            'sms_name' => '腾讯云',
            'sms_url' => 'https://cloud.tencent.com/product/sms',
            'appid' => '',
            'appkey' => '',
            'smsSign' => '',
        ],
        'duanxinbao' => [
            'sms_name' => '短信宝',
            'sms_url' => 'http://www.smsbao.com',
            'user' => '',
            'pass' => '',
            'signName' => '',
        ],
        'submail' => [
            'sms_name' => '赛邮云',
            'sms_url' => 'https://www.mysubmail.com',
            'appid' => '',
            'appkey' => '',
        ],
        'sendcloud' => [
            'sms_name' => 'SendCloud',
            'sms_url' => 'https://www.sendcloud.net',
            'sms_user' => '',
            'sms_key' => '',
        ],
        'ihuyi' => [
            'sms_name' => '互亿无线',
            'sms_url' => 'http://www.ihuyi.com',
            'apiid' => '',
            'apikey' => '',
        ],
        'chuanglan' => [
            'sms_name' => '创蓝253',
            'sms_url' => 'https://www.253.com',
            'api_send_url' => 'http://smssh1.253.com/msg/send/json',
            'api_variable_url' => 'http://smssh1.253.com/msg/variable/json',
            'api_balance_query_url' => 'http://smssh1.253.com/msg/balance/json',
            'api_account' => env('SMS_CHUANGLAN_API_ACCOUNT'),
            'api_password' => env('SMS_CHUANGLAN_API_PASSWORD'),
            'sms_sign' => env('SMS_CHUANGLAN_SMS_SIGN'),
        ],*/
    ],

    'templates' => [
        'yunpian' => [
            'verify_code' => env('SMS_YUNPIAN_VERIFICATION_CODE'),
            'user_account_pay' => '',
            'user_order_deliver_goods' => '',
            'seller_new_order' => '',
            'seller_order_account_pay' => '',
        ],
        'duanxinbao' => [
            'verify_code' => '您的验证码是%s。有效期为15分钟，请尽快验证！',
            'user_account_pay' => '已经收到您的订单，订单编号：%s，我们将尽快给您发货。',
            'user_order_deliver_goods' => '您的订单已发货，订单编号：%s，收货人：%s，联系电话：%s，收货地址：%s，请注意查收！',
            'seller_new_order' => '您有一条新订单，订单编号：%s，请注意查看。',
            'seller_order_account_pay' => '客户已付款，订单编号：%s，收货人：%s，联系电话：%s，订单金额：%s。',
        ],
        'chuanglan' => [
            'verify_code' => '您的验证码是{$var}。有效期为15分钟，请尽快验证！',
            'user_account_pay' => '已经收到您的订单，订单编号：{$var}，我们将尽快给您发货。',
            'user_order_deliver_goods' => '您的订单已发货，订单编号：{$var}，收货人：{$var}，联系电话：{$var}，收货地址：{$var}，请注意查收！',
            'seller_new_order' => '您有一条新订单，订单编号：{$var}，请注意查看。',
            'seller_order_account_pay' => '客户已付款，订单编号：{$var}，收货人：{$var}，联系电话：{$var}，订单金额：{$var}。',
        ],
        // Other...
    ],

];
