<?php

declare(strict_types=1);

use App\Middleware\CheckSettingsMiddleware;
use App\Middleware\FlashOldFormDataMiddleware;
use App\Middleware\SessionMiddleware;
use DI\NotFoundException;
use Middlewares\TrailingSlash;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    if (!($container = $app->getContainer())) {
        throw new NotFoundException('Could not get the container.');
    }

    $app->addMiddleware($container->get(SessionMiddleware::class));
    (require BOOTSTRAP_DIR . 'database.php')($container->get(Configuration::class)->getArray('db'));
    $app->addMiddleware(TwigMiddleware::createFromContainer($container->get(App::class)));
    $app->addMiddleware($container->get(WhoopsMiddleware::class));
    $app->addMiddleware($container->get(CheckSettingsMiddleware::class));
    $app->addMiddleware($container->get(TrailingSlash::class));
    $app->addMiddleware($container->get(FlashOldFormDataMiddleware::class));
    $app->addRoutingMiddleware();
};
