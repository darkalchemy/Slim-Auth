<?php

declare(strict_types=1);

use App\Exception\ExceptionHandler;
use App\Factory\LoggerFactory;
use DI\NotFoundException;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\Twig;

return function (App $app) {
    if (!($container = $app->getContainer())) {
        throw new NotFoundException('Could not get the container.');
    }
    $error = $container->get('settings')['error_handler_middleware'];

    $errorMiddleware = $app->addErrorMiddleware(
        $error['display_error_details'],
        $error['log_errors'],
        $error['log_error_details']
    );

    $errorMiddleware->setDefaultErrorHandler(
        new ExceptionHandler(
            $container->get(Messages::class),
            $app->getResponseFactory(),
            $container->get(Twig::class),
            $container->get(LoggerFactory::class),
        )
    );
};
