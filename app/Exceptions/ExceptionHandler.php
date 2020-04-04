<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Factory\LoggerFactory;
use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
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
    protected LoggerInterface       $logger;

    /**
     * ExceptionHandler constructor.
     *
     * @param Messages                 $flash           The flash
     * @param ResponseFactoryInterface $responseFactory The responseFactory
     * @param Twig                     $view            The view
     * @param LoggerFactory            $loggerFactory   The loggerFactory
     *
     * @throws Exception
     */
    public function __construct(Messages $flash, ResponseFactoryInterface $responseFactory, Twig $view, LoggerFactory $loggerFactory)
    {
        $this->flash           = $flash;
        $this->responseFactory = $responseFactory;
        $this->view            = $view;
        $this->logger          = $loggerFactory->addFileHandler('exception_handler.log')->createInstance('exception_handler');
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     *
     * @throws ReflectionException
     * @throws Throwable
     *
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
     * @param ValidationException $exception
     *
     * @return ResponseInterface
     */
    public function handleValidationException(ValidationException $exception)
    {
        $this->flash->addMessage('errors', $exception->getErrors());
        $this->logger->error('Validation exception', $exception->getErrors());

        return $this->responseFactory->createResponse()->withHeader('Location', $exception->getPath());
    }

    /**
     * @param Throwable $exception The exception
     *
     * @throws SyntaxError
     * @throws LoaderError
     * @throws RuntimeError
     *
     * @return ResponseInterface
     */
    public function handleHttpNotFoundException(Throwable $exception)
    {
        $this->logger->error('Http not found exception', ['error' => $exception->getMessage()]);

        return $this->view->render($this->responseFactory->createResponse(), 'pages/errors/404.twig')->withStatus(404);
    }

    public function HttpMethodNotAllowedException(Throwable $exception)
    {
        $this->logger->error('Http Method not allow exception', ['error' => $exception->getMessage()]);

        return $this->view->render($this->responseFactory->createResponse(), 'pages/errors/405.twig')->withStatus(405);
    }
}
