<?php

declare(strict_types=1);

use Delight\I18n\I18n;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Odan\Session\SessionInterface;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions($settings['root'] . '/bootstrap/container.php');
$settings['site']['di_compilation_path'] ? $containerBuilder->enableCompilation($settings['site']['di_compilation_path']) : null;

try {
    $container = $containerBuilder->build();
} catch (Exception $e) {
    dd($e->getMessage());
}

try {
    $app = $container->get(App::class);
} catch (DependencyException $e) {
    dd($e->getMessage());
} catch (NotFoundException $e) {
    dd($e->getMessage());
}

try {
    $session = $container->get(SessionInterface::class);
} catch (DependencyException $e) {
    dd($e->getMessage());
} catch (NotFoundException $e) {
    dd($e->getMessage());
}

try {
    $i18n = $container->get(I18n::class);
} catch (DependencyException $e) {
    dd($e->getMessage());
} catch (NotFoundException $e) {
    dd($e->getMessage());
}

(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/exceptions.php')($app);
require __DIR__ . '/validation.php';

return $app;
