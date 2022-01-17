<?php

declare(strict_types=1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class RedirectIfGuest.
 */
class RedirectIfGuest
{
    protected Messages $flash;
    protected RouteParserInterface $routeParser;

    /**
     * RedirectIfGuest constructor.
     *
     * @param Messages             $flash       The flash
     * @param RouteParserInterface $routeParser The routeParser
     */
    public function __construct(Messages $flash, RouteParserInterface $routeParser)
    {
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
    }

    /**
     * @param Request  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        if (Sentinel::guest()) {
            $this->flash->addMessage('status', _f('Please sign in before continuing'));

            $response = new Response();

            return $response->withHeader(
                'Location',
                $this->routeParser->urlFor('auth.signin') .
                    '?' .
                    http_build_query(['redirect' => $request->getUri()->getPath()])
            );
        }

        return $handler->handle($request);
    }
}
