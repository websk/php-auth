<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcached::class,
            'cache_key_prefix' => 'websk_auth',
            'servers' => [
                [
                    'host' => 'memcached',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_auth' => [
                'host' => 'mysql',
                'db_name' => 'db_auth',
                'user' => 'root',
                'password' => 'root',
                'dump_file_path' => \WebSK\Auth\AuthServiceProvider::DUMP_FILE_PATH

            ],
        ],
        'log_path' => '/var/www/log',
        'tmp_path' => '/var/www/tmp',
        'files_data_path' => '/var/www/public/files',
        'site_domain' => 'http://localhost',
        'site_full_path' => '/var/www',
        'site_name' => 'PHP Auth Demo',
        'site_title' => 'WebSK. PHP Auth Demo',
        'site_email' => 'support@websk.ru',
        'storages' => [
            'files' => [
                'adapter' => 'local',
                'root_path' => '/var/www/public/files',
                'url_path' => '/files',
                'allowed_extensions' => ['gif', 'jpeg', 'jpg', 'png', 'pdf', 'csv'],
                'allowed_types' => ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png', 'application/pdf', 'application/x-pdf', 'text/csv'],
            ]
        ],
        'auth' => [
            'salt' => 'webskauth',
            'layout_main' => '/var/www/views/layouts/layout.main.tpl.php',
            'layout_admin' => '/var/www/views/layouts/layout.main.tpl.php',
            'main_page_url' => '/',
            'admin_main_page_url' => '/admin/user'
        ]
    ],
];
