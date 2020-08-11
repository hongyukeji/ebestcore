<?php

return [
    'view_path' => resource_path('views'),
    'excludes' => ['common', 'errors', 'vendor', 'helper'],
    'templates' => [
        'frontend' => [
            'path_prefix' => 'frontend',
            'template' => 'default',
            'template_default' => 'default',
        ],
        'backend' => [
            'path_prefix' => 'backend',
            'template' => 'default',
            'template_default' => 'default',
        ],
        'mobile' => [
            'path_prefix' => 'mobile',
            'template' => 'default',
            'template_default' => 'default',
        ],
        'seller' => [
            'path_prefix' => 'seller',
            'template' => 'default',
            'template_default' => 'default',
        ],
        'common' => [
            'path_prefix' => 'common',
            'template' => 'default',
            'template_default' => 'default',
        ],
    ],
];
