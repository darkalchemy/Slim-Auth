<?php

declare(strict_types=1);

use Delight\I18n\I18n;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Slim\App;
use UMA\RedisSessionHandler;

$starttime = microtime(true);
require __DIR__ . '/../vendor/autoload.php';

$settings_path = realpath(__DIR__ . '/../') . '/config/settings.php';
if (!file_exists($settings_path)) {
    die(sprintf('%s does not exist.<br>please run:<br>cp %s %s<br>and edit as needed.', $settings_path, realpath(__DIR__ . '/../') . '/config/settings.example.php', $settings_path));
}
$settings         = require $settings_path;
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

try {
    $container->get(RedisSessionHandler::class);
} catch (DependencyException $e) {
    die($e->getMessage());
} catch (NotFoundException $e) {
    die($e->getMessage());
}

session_start($settings['session']);
(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/../routes/web.php')($app);
(require __DIR__ . '/exceptions.php')($app);
require __DIR__ . '/validation.php';

return $app;
