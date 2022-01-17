<?php

declare(strict_types=1);

namespace App\Controller\Auth\Password;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use App\Model\User;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;
use Odan\Session\PhpSession;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PasswordResetController.
 */
class PasswordResetController extends Controller
{
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected LoggerInterface       $logger;
    protected ValidationRules       $rules;
    protected PhpSession            $session;

    /**
     * PasswordResetController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param PhpSession           $session
     *
     * @throws Exception
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        LoggerFactory $loggerFactory,
        ValidationRules $rules,
        PhpSession $session
    ) {
        parent::__construct($session);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('password_reset_controller.log')
            ->createInstance('password_reset_controller');
        $this->rules       = $rules;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        $params = array_clean($request->getQueryParams(), [
            'email',
            'code',
        ]);
        if (!$this->reminderCodeExists(User::whereEmail($email = $params['email'] ?? null)
            ->first(), $code = $params['code'] ?? null)) {
            $this->flash->addMessage('status', _f('Invalid reset code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('home'));
        }

        return $this->view->render($response, 'pages/auth/password/reset.twig', compact('email', 'code'));
    }

    /**
     * @param Request $request
     * @param Response      $response
     *
     * @throws ValidationException
     *
     * @return Response
     */
    public function reset(Request $request, Response $response): Response
    {
        $data = $this->validate($request, array_merge_recursive(
            $this->rules->required('email'),
            $this->rules->required('code'),
            $this->rules->password(),
            $this->rules->confirm_password()
        ));

        $params = array_clean($data, [
            'email',
            'code',
            'password',
        ]);
        if (!$this->reminderCodeExists($user = User::whereEmail($params['email'])->first(), $code = $params['code'])) {
            $this->logger->error('Invalid reset code', $data);
            $this->flash->addMessage('status', _f('Invalid reset code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('home'));
        }

        Sentinel::getReminderRepository()->complete($user, $code, $params['password']);
        $this->flash->addMessage('status', _f('Your password has been reset and you can now sign in.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
    }

    /**
     * @param null|User   $user
     * @param null|string $code
     *
     * @return bool
     */
    protected function reminderCodeExists(?User $user, ?string $code): bool
    {
        if (!$user) {
            return false;
        }

        if (!Sentinel::getReminderRepository()->exists($user, $code)) {
            return false;
        }

        return true;
    }
}
