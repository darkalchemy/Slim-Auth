<?php

declare(strict_types=1);

use Monolog\Logger;

$scheme = get_scheme() === 'https';

date_default_timezone_set('UTC');
ini_set('default_charset', 'utf-8');
ini_set('max_execution_time', '300');

$settings['site_name'] = 'Slim-Auth';
$settings['root']      = dirname(__DIR__);
$settings['tmp']       = $settings['root'] . '/var/tmp';
$settings['site']      = [
    'app_env'                  => 'DEVELOPMENT', // DEVELOPMENT PRODUCTION
    'root'                     => dirname(__DIR__),
    'di_compilation_path'      => null,
    'db_sockets'               => false,
    'error_handler_middleware' => [
        'display_error_details' => true,
        'log_errors'            => true,
        'log_error_details'     => true,
    ],
];

$settings['db'] = [
    'driver'        => 'mysql',
    'host'          => '127.0.0.1',
    'port'          => 3306,
    'database'      => 'slim-auth',
    'username'      => 'root',
    'password'      => '',
    'prefix'        => '',
    'charset'       => 'utf8mb4',
    'encoding'      => 'utf8mb4',
    'collation'     => 'utf8mb4_unicode_ci',
    'strict'        => false,
    'timezone'      => null,
    'cacheMetadata' => false,
    'log'           => false,
    'options'       => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
    ],
];

$settings['router'] = [
    'cache_file' => '',
];

$settings['twig'] = [
    'path'  => $settings['root'] . '/resources/views',
    'cache' => null,
];

$settings['webpack'] = [
    'manifest' => $settings['root'] . '/public/resources/manifest.json',
    'js_path'  => '/resources/',
    'css_path' => '/resources/',
];

$settings['bad_words'] = [
    'owner',
    'staff',
    'admin',
    'administrator',
    'sysop',
    'user',
];

$settings['mail'] = [
    'smtp_enable'     => false,
    'smtp_host'       => 'smtp.gmail.com',
    'smtp_auth'       => true,
    'smtp_username'   => 'username@gmail.com',
    'smtp_password'   => 'gmail password',
    'smtp_secure'     => 'tls',
    'smtp_port'       => 587,
    'smtp_from_email' => 'username@gmail.com',
    'smtp_from_user'  => $settings['site_name'] . ' Staff',
];

$settings['logger'] = [
    'name'  => 'simple',
    'path'  => $settings['root'] . '/var/logs/',
    'level' => Logger::DEBUG,
];

$settings['cookies'] = [
    'name'      => ($scheme ? '__Secure-' : '') . str_replace(' ', '_', $settings['site_name']),
    'http_only' => '1',
    'secure'    => $scheme ? '1' : '0',
    'samesite'  => 'Lax',
];

$settings['session'] = [
    'name'                   => str_replace(' ', '_', $settings['site_name']),
    'sid_length'             => (ini_get('session.save_handler') != 'files' ? '256' : '128'),
    'lazy_write'             => '0',
    'sid_bits_per_character' => '6',
    'use_strict_mode'        => '1',
    'use_trans_sid'          => '0',
    'use_cookies'            => '1',
    'use_only_cookies'       => '1',
    'cookie_domain'          => '',
    'cookie_httponly'        => '1',
    'cookie_lifetime'        => '0',
    'gc_divisor'             => 1,
    'gc_maxlifetime'         => '1350',
    'cookie_path'            => '/',
    'cookie_samesite'        => 'Lax',
    'cookie_secure'          => $scheme ? '1' : '0',
];

if ($settings['site']['db_sockets']) {
    $settings['db']['unix_socket'] = '/var/run/mysqld/mysqld.sock';
    unset($settings['db']['host'], $settings['db']['port']);
}
if ($settings['site']['app_env'] === 'PRODUCTION') {
    $settings['site']['di_compilation_path']                               = $settings['root'] . '/var/cache';
    $settings['site']['error_handler_middleware']['display_error_details'] = false;
    $settings['router']['cache_file']                                      = $settings['root'] . '/var/cache/router.cache';
    $settings['twig']['cache']                                             = $settings['root'] . '/resources/views/cache';
}

return $settings;
