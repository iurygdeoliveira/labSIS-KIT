<?php

return [
    'database' => [
        'table_name' => 'composer_versions',
    ],
    'widgets' => [
        'dependency' => [
            'show_direct_only' => true,
        ],
    ],
    'paths' => [
        'php_path' => env('PHP_PATH', ''), // Path to the PHP executable, if default path not working
        'composer_path' => env('COMPOSER_PATH', ''), // Path to the Composer executable, if default path not working
    ],
];
