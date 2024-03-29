<?php

declare(strict_types=1);

namespace App\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class RedirectIfNotAuthenticated.
 */
class RedirectIfNotAuthenticated
{
    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * @param Messages             $flash       The flash
     * @param RouteParserInterface $routeParser The routeParser
     */
    public function __construct(
        Messages $flash,
        RouteParserInterface $routeParser,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->flash           = $flash;
        $this->routeParser     = $routeParser;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (Sentinel::guest()) {
            $this->flash->addMessage('status', __f('Please sign in before continuing'));

            $response = $this->responseFactory->createResponse();

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
