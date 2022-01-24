<?php

declare(strict_types=1);

use App\Factory\ContainerFactory;
use Delight\I18n\I18n;
use DI\DependencyException;
use DI\NotFoundException;
use Slim\App;
use UMA\RedisSessionHandler;

$startTime = hrtime(true);
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/constants.php';

try {
    $container = (new ContainerFactory())->createContainer();
} catch (Exception $e) {
    exit($e->getMessage());
}

try {
    $app = $container->get(App::class);
} catch (DependencyException|NotFoundException $e) {
    exit($e->getMessage());
}

try {
    $i18n = $container->get(I18n::class);
} catch (DependencyException|NotFoundException $e) {
    exit($e->getMessage());
}

try {
    $container->get(RedisSessionHandler::class);
} catch (DependencyException|NotFoundException $e) {
    exit($e->getMessage());
}

return $app ?? null;
