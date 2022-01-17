<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use Nyholm\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class DashboardController.
 */
class DashboardController
{
    protected Twig $view;

    /**
     * DashboardController constructor.
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
        return $this->view->render($response, 'pages/dashboard/index.twig');
    }
}
