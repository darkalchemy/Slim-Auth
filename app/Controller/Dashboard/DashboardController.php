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
    /**
     * @var Twig
     */
    protected Twig $view;

    /**
     * @param Twig $view The view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * @param ResponseInterface $response The response
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'pages/dashboard/index.twig');
    }
}
