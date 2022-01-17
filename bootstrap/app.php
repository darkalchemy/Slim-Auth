<?php

declare(strict_types=1);

use App\Factory\ContainerFactory;
use Delight\I18n\I18n;
use Slim\App;
use UMA\RedisSessionHandler;

$startTime = microtime(true);
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/constants.php';

try {
    $container = (new ContainerFactory())->createContainer();
} catch (Exception $e) {
    die($e->getMessage());
}

$app  = $container->get(App::class);
$i18n = $container->get(I18n::class);
$container->get(RedisSessionHandler::class);

return $app;
