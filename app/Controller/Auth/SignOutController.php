<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Nyholm\Psr7\Response;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class SignOutController.
 */
class SignOutController
{
    protected RouteParserInterface $routeParser;

    /**
     * SignOutController constructor.
     *
     * @param RouteParserInterface $routeParser
     */
    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Response $response): Response
    {
        Sentinel::logout();

        return $response->withHeader('Location', $this->routeParser->urlFor('home'));
    }
}
