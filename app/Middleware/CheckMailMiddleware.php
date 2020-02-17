<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

class CheckMailMiddleware implements MiddlewareInterface
{
    protected array $settings;
    protected Messages $flash;

    public function __construct(array $settings, Messages $flash)
    {
        $this->settings = $settings;
        $this->flash = $flash;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (!$this->settings['smtp_enable']) {
            $this->flash->addMessage('error', _f('You must set up mail settings to send mail.'));
        }

        return $response;
    }
}
