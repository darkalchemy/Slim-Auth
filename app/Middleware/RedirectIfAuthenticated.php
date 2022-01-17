<?php

declare(strict_types=1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
    /**
     * @param Request $request
     * @param RequestHandlerInterface $handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        if (!Sentinel::guest()) {
            $response = new Response();

            return $response->withHeader('Location', '/');
        }

        return $handler->handle($request);
    }
}
