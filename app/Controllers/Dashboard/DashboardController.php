<?php

declare(strict_types=1);

namespace App\Controllers\Dashboard;

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
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/dashboard/index.twig');
    }
}
