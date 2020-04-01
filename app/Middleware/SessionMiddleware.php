<?php

declare(strict_types=1);

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

/**
 * Class SessionMiddleware.
 */
class SessionMiddleware implements MiddlewareInterface
{
    protected SessionInterface $session;
    protected Messages $flash;

    /**
     * SessionMiddleware constructor.
     *
     * @param SessionInterface $session
     * @param Messages         $flash
     */
    public function __construct(SessionInterface $session, Messages $flash)
    {
        $this->session = $session;
        $this->flash   = $flash;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        $this->flash->__construct($_SESSION);
        $response = $handler->handle($request);
        if (!$this->session->has('regen') || $this->session->get('regen') < time()) {
            $this->session->regenerateId();
            $this->session->set('regen', time() + 300);
        }

        return $response;
    }
}
