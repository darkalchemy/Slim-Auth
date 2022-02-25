<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

/**
 * Class FlashOldFormData.
 */
class FlashOldFormDataMiddleware implements MiddlewareInterface
{
    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @param Messages $flash The flash
     */
    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($params = $request->getParsedBody())) {
            $this->flash->addMessage('old', $params);
        }

        return $handler->handle($request);
    }
}
