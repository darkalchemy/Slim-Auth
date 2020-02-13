<?php

declare(strict_types = 1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
        $this->flash = $flash;
        $this->routeParser = $routeParser;
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $response = $handler->handle($request);

        if (Sentinel::guest()) {
            $this->flash->addMessage('status', 'Please sign in before continuing');

            $response = $response
                ->withHeader(
                    'Location',
                    $this->routeParser->urlFor('auth.signin') .
                    '?' .
                    http_build_query(['redirect' => $request->getUri()->getPath()])
                );
        }

        return $response;
    }
}
