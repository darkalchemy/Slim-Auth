<?php

declare(strict_types = 1);

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';
(require __DIR__ . '/session.php')($settings['cookies']);
session_start();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$settings['di_compilation_path'] ? $containerBuilder->enableCompilation($settings['di_compilation_path']) : null;
$container = $containerBuilder->build();
$app = $container->get(App::class);

(require __DIR__ . '/database.php')($container->get(Capsule::class), $settings['cookies']);
(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/exceptions.php')($app);
require __DIR__ . '/validation.php';

return $app;
