<?php

declare(strict_types=1);

use App\Middleware\CheckMailMiddleware;
use App\Middleware\FlashOldFormDataMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\SetLocaleMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    $container = $app->getContainer();

    (require __DIR__ . '/database.php')($container->get(Capsule::class));
    $app->addMiddleware($container->get(Guard::class));
    $app->addMiddleware($container->get(FlashOldFormDataMiddleware::class));
    $app->addMiddleware(TwigMiddleware::createFromContainer($container->get(App::class)));
    $app->addMiddleware($container->get(WhoopsMiddleware::class));
    $app->addMiddleware($container->get(CheckMailMiddleware::class));
    $app->addMiddleware($container->get(SetLocaleMiddleware::class));
    $app->addMiddleware($container->get(SessionMiddleware::class));
    $app->addRoutingMiddleware();
};
