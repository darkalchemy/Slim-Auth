<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
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
     * @param ResponseInterface $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/home/index.twig');
    }
}
