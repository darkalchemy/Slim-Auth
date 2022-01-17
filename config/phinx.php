<?php

declare(strict_types=1);

use Selective\Config\Configuration;

$container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();
$db        = $container->get(Configuration::class)->getArray('db');

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds'      => 'database/seeds',
    ],
    'migration_base_class' => 'App\Migrations\Migration',
    'templates'            => [
        'file' => 'app/Migrations/MigrationStub.php.stub',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default'                 => [
            'adapter' => $db['driver'],
            'host'    => $db['host'] ?? null,
            'port'    => $db['port'] ?? null,
            'socket'  => $db['socket'] ?? null,
            'name'    => $db['database'],
            'user'    => $db['username'],
            'pass'    => $db['password'],
        ],
    ],
    'foreign_keys'             => true,
    'default_migration_prefix' => 'db_schema_',
    'generate_migration_name'  => true,
    'mark_generated_migration' => true,
];
