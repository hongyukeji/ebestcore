<?php

return [
    'requirements' => [
        'php_version' => '7.1.3',
        'extensions' => [
            'pdo_mysql', 'openssl', 'gd', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath',
            'fileinfo',
            //'mcrypt', 'popen',
        ],
        'dir_permissions' => [
            'bootstrap/cache' => '755',
            'modules' => '755',
            'plugins' => '755',
            'storage' => '755',
        ],
        'write_files' => [
            '.env' => '755',
        ],
        'disable_functions' => [
            'exec', 'proc_open', 'putenv',
            //'system',
        ],
    ],
];
