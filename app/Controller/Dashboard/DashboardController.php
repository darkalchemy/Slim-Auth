<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use Psr\Http\Message\ResponseInterface;
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
     * @param ResponseInterface $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'pages/dashboard/index.twig');
    }
}
