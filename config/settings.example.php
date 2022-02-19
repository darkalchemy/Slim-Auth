<?php

declare(strict_types=1);

date_default_timezone_set('UTC');
$siteName = 'Slim-Auth';

$settings = [
    'site_name'           => $siteName,
    'di_compilation_path' => CACHE_DIR,
    'router_cache_file'   => CACHE_DIR . 'router.cache',
    'environment'         => 'DEVELOPMENT', // DEVELOPMENT or PRODUCTION
    'session'             => [
        'name'                   => str_replace(' ', '_', $siteName),
        'sid_length'             => '128',
        'cache_expire'           => '300',
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
        'username'      => '',
        'password'      => '',
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
        'cache' => VIEWS_DIR . 'cache' . DIRECTORY_SEPARATOR,
        'charset' => 'UTF-8',
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
        'smtp_username'   => '',
        'smtp_password'   => '',
        'smtp_secure'     => 'tls',
        'smtp_port'       => 587,
        'smtp_from_email' => '',
        'smtp_from_user'  => $siteName . ' Staff',
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
