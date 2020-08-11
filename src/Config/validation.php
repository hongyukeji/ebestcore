<?php

return [
    'mobile' => '/^1\d{10}$/',
    'username' => '/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',
    'username_disable_keyword' => 'admin,administrator',
    'price' => '/^(?!0\d|[0.]+$)\d{1,8}(\.\d{1,2})?$/',
];
