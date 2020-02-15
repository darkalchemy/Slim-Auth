<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Selective\Config\Configuration;
use Slim\Flash\Messages;

class CheckMailMiddleware implements MiddlewareInterface
{
    protected ContainerInterface $container;
    protected Messages $flash;

    public function __construct(ContainerInterface $container, Messages $flash)
    {
        $this->container = $container;
        $this->flash = $flash;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if (!$this->container->get(Configuration::class)->getArray('mail')['smtp_enable']) {
            $this->flash->addMessage('error', 'You must set up mail settings to use this.');
        }

        return $response;
    }
}
