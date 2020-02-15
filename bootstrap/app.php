<?php

declare(strict_types = 1);

use DI\ContainerBuilder;
use Odan\Session\SessionInterface;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$settings['di_compilation_path'] ? $containerBuilder->enableCompilation($settings['di_compilation_path']) : null;
$container = $containerBuilder->build();
$app = $container->get(App::class);
$session = $container->get(SessionInterface::class);
$session->start();

(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/exceptions.php')($app);
require __DIR__ . '/validation.php';

return $app;
