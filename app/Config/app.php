<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => $_ENV['APP_ENV'] ?: 'production',

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Database config
    |--------------------------------------------------------------------------
    |
    | Database settings like DSN, username, password etc.
    | Recommended "ERRMODE_EXCEPTION" to throw all exceptions
    |
    */

    'database' => [
        'dsn' => env('DB_DSN', 'sqlite:' . dirname(__DIR__, 1) . '/storage/sqlite.db'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', 'root'),
        'charset' => env('DB_CHAR', 'utf8mb4'),
        'collation' => env('DB_COLL', 'utf8mb4_unicode_ci'),
        'options' => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ],
    ],
];
