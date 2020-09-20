<?php

require_once __DIR__ . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

return
    [
        'paths' => [
            'migrations' => './app/db/migrations',
            'seeds' => './app/db/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'production_db',
                'user' => 'root',
                'pass' => '',
                'port' => '3306',
                'charset' => 'utf8mb4',
            ],
            'development' => [
                'adapter' => 'sqlite',
                'host' => $_ENV['DB_HOST'],
                'name' => './app/storage/example',
                'user' => $_ENV['DB_USER'],
                'pass' => $_ENV['DB_PASS'],
                'suffix' => '.db',
                'port' => '3306',
                'charset' => 'utf8mb4',
            ]
        ],
        'version_order' => 'creation'
    ];