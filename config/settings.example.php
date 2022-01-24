<?php

declare(strict_types=1);

date_default_timezone_set('UTC');
ini_set('default_charset', 'utf-8');
ini_set('max_execution_time', '300');

$settings = [
    'site_name'           => SITE_NAME,
    'di_compilation_path' => ENV === 'PRODUCTION' ? CACHE_DIR : null,
    'router_cache_file'   => ENV === 'PRODUCTION' ? CACHE_DIR . 'router.cache' : '',
    'session'             => [
        'name'                   => str_replace(' ', '_', SITE_NAME),
        'save_handler'           => SESSION_HANDLER,
        'save_path'              => SESSION_HANDLER === 'redis' ? 'unix:///dev/shm/redis.sock?database=1' : '',
        'sid_length'             => '128',
        'cache_expire'           => '0',
        'lazy_write'             => '0',
        'sid_bits_per_character' => '5',
        'use_strict_mode'        => '1',
        'use_trans_sid'          => '0',
        'use_cookies'            => '1',
        'use_only_cookies'       => '1',
        'cookie_domain'          => '',
        'cookie_httponly'        => '1',
        'cookie_lifetime'        => '0',
        'cookie_path'            => '/',
        'cookie_samesite'        => 'Lax',
        'cookie_secure'          => '0',
    ],
    'error_handler_middleware' => [
        'display_error_details' => true,
        'log_errors'            => true,
        'log_error_details'     => true,
    ],
    'db' => [
        'driver'        => 'mysql',
        'use_socket'    => true,
        'host'          => '127.0.0.1',
        'port'          => 3306,
        'socket'        => 'localhost;unix_socket=/var/run/mysqld/mysqld.sock',
        'database'      => 'slim_auth',
        'username'      => 'username',
        'password'      => 'password',
        'prefix'        => '',
        'charset'       => 'utf8mb4',
        'encoding'      => 'utf8mb4',
        'collation'     => 'utf8mb4_unicode_ci',
        'strict'        => true,
        'timezone'      => null,
        'cacheMetadata' => false,
        'log'           => true,
        'attributes'    => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ],
    ],
    'twig' => [
        'path'  => VIEWS_DIR,
        'cache' => ENV === 'PRODUCTION' ? VIEWS_DIR . 'cache' . DIRECTORY_SEPARATOR : null,
    ],
    'webpack' => [
        'manifest' => PUBLIC_RESOURCES_DIR . 'manifest.json',
    ],
    'logger' => [
        'name' => 'simple',
        'path' => LOGS_DIR,
    ],
    'mail' => [
        'smtp_enable'     => false,
        'smtp_host'       => 'smtp.gmail.com',
        'smtp_auth'       => true,
        'smtp_username'   => 'email address',
        'smtp_password'   => 'password',
        'smtp_secure'     => 'tls',
        'smtp_port'       => 587,
        'smtp_from_email' => 'email address',
        'smtp_from_user'  => SITE_NAME . ' Staff',
    ],
    'bad_words' => [
        'owner',
        'staff',
        'admin',
        'administrator',
        'sysop',
        'user',
    ],
];

return $settings;
