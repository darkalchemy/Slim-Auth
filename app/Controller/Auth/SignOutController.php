<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class SignOutController.
 */
class SignOutController
{
    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @param RouteParserInterface $routeParser The routeParser
     */
    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    /**
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        Sentinel::logout();

        return $response->withHeader('Location', $this->routeParser->urlFor('home'));
    }
}
