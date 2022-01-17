<?php

declare(strict_types=1);

namespace App\Middleware;

use Odan\Session\SessionInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SessionMiddleware.
 */
class SessionMiddleware implements MiddlewareInterface
{
    protected SessionInterface $session;

    /**
     * SessionMiddleware constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
        if (!$this->session->has('regen') || $this->session->get('regen') < time()) {
            $this->session->regenerateId();
            $this->session->set('regen', time() + 300);
        }

        return $handler->handle($request);
    }
}
