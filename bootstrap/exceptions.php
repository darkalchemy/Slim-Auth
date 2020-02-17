<?php

declare(strict_types=1);

use App\Exceptions\ExceptionHandler;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\Twig;

return static function (App $app) {
    $container = $app->getContainer();
    $error = $container->get(Configuration::class)->getArray('site.error_handler_middleware');

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
        )
    );
};
