<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class SignOutController.
 */
class SignOutController
{
    protected RouteParserInterface $routeParser;

    /**
     * SignOutController constructor.
     */
    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    /**
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response)
    {
        Sentinel::logout();

        return $response->withHeader('Location', $this->routeParser->urlFor('home'));
    }
}
