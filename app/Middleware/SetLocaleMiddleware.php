<?php

declare(strict_types=1);

namespace App\Middleware;

use Delight\I18n\I18n;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

class SetLocaleMiddleware implements MiddlewareInterface
{
    protected I18n $i18n;
    protected Messages $flash;

    public function __construct(I18n $i18n, Messages $flash)
    {
        $this->i18n  = $i18n;
        $this->flash = $flash;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        try {
            $this->i18n->setLocaleAutomatically();

            return $response;
        } catch (Exception $e) {
            $this->flash->addMessage('error', 'The locale requested by the user is not supported');
        }

        try {
            $this->i18n->setLocaleManually('en-US');

            return $response;
        } catch (Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }

        return $response;
    }
}
