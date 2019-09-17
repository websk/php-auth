<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcache::class,
            'cache_key_prefix' => 'skif',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_auth' => [
                'host' => 'localhost',
                'db_name' => 'db_auth',
                'user' => 'root',
                'password' => 'root',
            ],
            'db_logger' => [
                'host' => 'localhost',
                'db_name' => 'db_logger',
                'user' => 'root',
                'password' => 'root',
            ],
        ],
        'log_path' => '/var/www/log',
        'tmp_path' => '/var/www/tmp',
        'files_data_path' => '/var/www/php-auth/public/files',
        'site_domain' => 'http://localhost',
        'site_full_path' => '/var/www/php-auth',
        'site_name' => 'PHP Auth Demo',
        'site_title' => 'WebSK. PHP Auth Demo',
        'site_email' => 'support@websk.ru',
        'auth' => [
            'salt' => 'webskskif',
            'layout_main' => '/var/www/php-auth/views/layouts/layout.main.tpl.php',
            'layout_skif' => '/var/www/php-auth/views/layouts/layout.main.tpl.php',
            'main_page_url' => '/',
            'skif_main_page_url' => '/admin/user/'
        ]
    ],
];
