<?php

declare(strict_types = 1);

use App\Middleware\FlashOldFormData;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    $container = $app->getContainer();

    $app->addMiddleware($container->get(Guard::class));
    $app->add($container->get(FlashOldFormData::class));
    $app->addMiddleware(TwigMiddleware::createFromContainer($container->get(App::class)));
    $app->addRoutingMiddleware();
    $app->addMiddleware($container->get(WhoopsMiddleware::class));
};
