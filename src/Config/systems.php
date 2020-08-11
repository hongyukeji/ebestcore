<?php

return [
    'routes' => [
        'root' => [
            'uri' => env('SYSTEMS_ROUTES_ROOT_URI'),
            'status' => env('SYSTEMS_ROUTES_ROOT_STATUS', false),
        ],
        'frontend' => [
            'prefix' => env('SYSTEMS_ROUTES_FRONTEND_PREFIX', ''),
            'domain' => '',
            'status' => true,
        ],
        'backend' => [
            'prefix' => env('SYSTEMS_ROUTES_BACKEND_PREFIX', 'admin'),
            'domain' => '',
            'status' => true,
        ],
        'mobile' => [
            'prefix' => env('SYSTEMS_ROUTES_MOBILE_PREFIX', 'mobile'),
            'domain' => '',
            'status' => true,
        ],
        'seller' => [
            'prefix' => env('SYSTEMS_ROUTES_SELLER_PREFIX', 'seller'),
            'domain' => '',
            'status' => true,
        ],
        'api' => [
            'prefix' => env('SYSTEMS_ROUTES_API_PREFIX', 'api'),
            'domain' => '',
            'status' => true,
        ],
        'helper' => [
            'prefix' => env('SYSTEMS_ROUTES_HELPER_PREFIX', 'helper'),
            'domain' => '',
            'status' => true,
        ],
        'common' => [
            'prefix' => env('SYSTEMS_ROUTES_COMMON_PREFIX', 'common'),
            'domain' => '',
            'status' => true,
        ],
    ],
    'security' => [
        'administrator' => env('SYSTEMS_SECURITY_ADMINISTRATOR', 'Administrator'),
        'default_password' => env('SYSTEMS_SECURITY_DEFAULT_PASSWORD', '123456'),
        'strict_check_mode' => false,
        'system_protection' => false,
        'role_name_implode_symbol' => ',',
    ],

    'api_token' => env('SYSTEMS_API_TOKEN'),
];
