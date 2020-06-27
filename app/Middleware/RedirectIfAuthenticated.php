<?php

declare(strict_types=1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (!Sentinel::guest()) {
            $response = new Response();

            return $response->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}
