<?php

declare(strict_types=1);

use App\Middleware\CheckSettingsMiddleware;
use App\Middleware\FlashOldFormDataMiddleware;
use App\Middleware\SessionMiddleware;
use Middlewares\TrailingSlash;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    $container = $app->getContainer();

    (require __DIR__ . '/database.php')($container->get(Configuration::class)->getArray('db'));
    $app->addMiddleware(TwigMiddleware::createFromContainer($container->get(App::class)));
    $app->addMiddleware($container->get(WhoopsMiddleware::class));
    $app->addMiddleware($container->get(CheckSettingsMiddleware::class));
    $app->addMiddleware($container->get(TrailingSlash::class));

    $app->addMiddleware($container->get(FlashOldFormDataMiddleware::class));
    $app->addMiddleware($container->get(Guard::class));
    $app->addMiddleware($container->get(SessionMiddleware::class));
    $app->addRoutingMiddleware();
};
