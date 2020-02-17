<?php

declare(strict_types=1);

namespace App\Exceptions;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ExceptionHandler.
 */
class ExceptionHandler
{
    protected Messages $flash;
    protected ResponseFactoryInterface $responseFactory;
    protected Twig $view;

    /**
     * ExceptionHandler constructor.
     *
     * @param Messages                 $flash           The flash
     * @param ResponseFactoryInterface $responseFactory The responseFactory
     * @param Twig                     $view            The view
     */
    public function __construct(Messages $flash, ResponseFactoryInterface $responseFactory, Twig $view)
    {
        $this->flash = $flash;
        $this->responseFactory = $responseFactory;
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     * @throws ReflectionException
     * @throws Throwable
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, Throwable $exception)
    {
        if (method_exists($this, $handle = 'handle' . (new ReflectionClass($exception))->getShortName())) {
            return $this->{$handle}($exception);
        }

        throw $exception;
    }

    /**
     * @param Throwable $exception The exception
     *
     * @return ResponseInterface
     */
    public function handleValidationException(Throwable $exception)
    {
        $this->flash->addMessage('errors', $exception->getErrors());

        return $this->responseFactory
            ->createResponse()
            ->withHeader('Location', $exception->getPath());
    }

    /**
     * @param Throwable $exception The exception
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function handleHttpNotFoundException(Throwable $exception)
    {
        return $this->view->render(
            $this->responseFactory->createResponse(),
            'pages/errors/404.twig'
        )->withStatus(404);
    }
}
