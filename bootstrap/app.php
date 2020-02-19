<?php

declare(strict_types=1);

use Delight\I18n\I18n;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$settings         = require __DIR__ . '/../config/settings.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($settings['root'] . '/bootstrap/container.php');
$settings['site']['di_compilation_path'] ? $containerBuilder->enableCompilation($settings['site']['di_compilation_path']) : null;

try {
    $container = $containerBuilder->build();
} catch (Exception $e) {
    die($e->getMessage());
}

try {
    $app = $container->get(App::class);
} catch (DependencyException $e) {
    die($e->getMessage());
} catch (NotFoundException $e) {
    die($e->getMessage());
}

try {
    $i18n = $container->get(I18n::class);
} catch (DependencyException $e) {
    die($e->getMessage());
} catch (NotFoundException $e) {
    die($e->getMessage());
}

(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/exceptions.php')($app);
require __DIR__ . '/validation.php';

return $app;
