<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

/**
 * Class FlashOldFormData.
 */
class FlashOldFormData
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
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (!empty($params = $request->getParsedBody())) {
            $this->flash->addMessage('old', $params);
        }

        return $handler->handle($request);
    }
}
