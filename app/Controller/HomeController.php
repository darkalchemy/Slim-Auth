<?php

declare(strict_types=1);

namespace App\Controller;

use Nyholm\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController.
 */
class HomeController
{
    protected Twig $view;

    /**
     * HomeController constructor.
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * @param Response $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return Response
     */
    public function __invoke(Response $response): Response
    {
        return $this->view->render($response, 'pages/home/index.twig');
    }
}
