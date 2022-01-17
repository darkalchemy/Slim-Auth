<?php

declare(strict_types=1);

namespace App\Middleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

/**
 * Class FlashOldFormData.
 */
class FlashOldFormDataMiddleware implements MiddlewareInterface
{
    protected Messages $flash;

    /**
     * FlashOldFormData constructor.
     *
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
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): Response
    {
        if (!empty($params = $request->getParsedBody())) {
            $this->flash->addMessage('old', $params);
        }

        return $handler->handle($request);
    }
}
