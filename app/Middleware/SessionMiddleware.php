<?php

declare(strict_types=1);

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Csrf\Guard;

/**
 * Class SessionMiddleware.
 */
class SessionMiddleware implements MiddlewareInterface
{
    protected SessionInterface $session;
    protected Guard $guard;

    /**
     * SessionMiddleware constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session, Guard $guard)
    {
        $this->session = $session;
        $this->guard   = $guard;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->session->isStarted() && !headers_sent()) {
            $this->session->start();
        }
        if (!$this->session->has('regen') || $this->session->get('regen') < time()) {
            $this->session->regenerateId();
            $this->session->set('regen', time() + 300);
        }

        $this->guard->setStorage($this);

        return $handler->handle($request);
    }
}
