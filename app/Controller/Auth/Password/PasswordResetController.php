<?php

declare(strict_types=1);

namespace App\Controller\Auth\Password;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use App\Model\User;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Delight\I18n\I18n;
use Exception;
use Monolog\Logger;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
    protected Twig $view;
    protected Messages $flash;
    protected RouteParserInterface $routeParser;
    protected LoggerInterface $logger;
    protected ValidationRules $rules;
    protected SessionInterface $session;
    protected I18n $i18n;

    /**
     * PasswordResetController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param SessionInterface     $session
     * @param I18n                 $i18n
     *
     * @throws Exception
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        LoggerFactory $loggerFactory,
        ValidationRules $rules,
        SessionInterface $session,
        I18n $i18n
    ) {
        parent::__construct($session, $i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('password_reset_controller.log', Logger::DEBUG)
            ->createInstance('password_reset_controller');
        $this->rules = $rules;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function reset(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $this->validate($request, array_merge_recursive(
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
