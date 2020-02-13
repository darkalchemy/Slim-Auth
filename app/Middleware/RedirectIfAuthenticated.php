<?php

declare(strict_types = 1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
    protected ResponseInterface $response;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (!Sentinel::guest()) {
            return $this->response->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}
