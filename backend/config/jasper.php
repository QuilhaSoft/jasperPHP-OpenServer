
<?php

return [
    'jasper_path' => base_path('vendor/quilhasoft/jasperphp'),
    'reports_path' => storage_path('app/reports'),
    'drivers' => [
        'mysql' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'port' => env('DB_PORT', '3306'),
        ],
        // Adicione outros drivers de banco de dados aqui
    ],
];
