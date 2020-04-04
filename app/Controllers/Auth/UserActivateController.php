<?php

declare(strict_types=1);

namespace App\Controllers\Auth\Password;

use App\Controllers\Controller;
use App\Factory\LoggerFactory;
use App\Models\User;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;
use Odan\Session\PhpSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class UserActivateController.
 */
class UserActivateController extends Controller
{
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected LoggerInterface       $logger;
    protected PhpSession            $session;

    /**
     * SignUpController constructor.
     *
     * @param Messages             $flash         The response
     * @param RouteParserInterface $routeParser   The routeParser
     * @param LoggerFactory        $loggerFactory The logger
     * @param PhpSession           $session
     *
     * @throws Exception
     */
    public function __construct(Messages $flash, RouteParserInterface $routeParser, LoggerFactory $loggerFactory, PhpSession $session)
    {
        parent::__construct($session);
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('activate_controller.log')->createInstance('activate_controller');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = array_clean($request->getQueryParams(), [
            'email',
            'code',
        ]);

        if (!$this->activationCodeExists($user = User::whereEmail($email = $params['email'] ?? null)->first(), $code = $params['code'] ?? null)) {
            $this->logger->error('Invalid activation code.');
            $this->flash->addMessage('error', _f('Invalid activation code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('auth.signup'));
        }

        Sentinel::getActivationRepository()->complete($user, $code);
        $role = Sentinel::findRoleByName('User');
        $role->users()->attach($user);
        $this->flash->addMessage('success', _f('Your email has been confirmed and your account has been activated. You can now sign in.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
    }

    /**
     * @param null|User   $user
     * @param null|string $code
     *
     * @return bool
     */
    protected function activationCodeExists(?User $user, ?string $code)
    {
        if (!$user) {
            return false;
        }

        if (!Sentinel::getActivationRepository()->exists($user, $code)) {
            return false;
        }

        return true;
    }
}
