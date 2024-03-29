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
    /**
     * @var Twig
     */
    protected Twig $view;

    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ValidationRules
     */
    protected ValidationRules $rules;

    /**
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * @param Twig                 $view          The view
     * @param Messages             $flash         The flash
     * @param RouteParserInterface $routeParser   The routeParser
     * @param LoggerFactory        $loggerFactory The loggerFactory
     * @param ValidationRules      $rules         The rules
     * @param I18n                 $i18n          The i18n
     *
     * @throws Exception
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        LoggerFactory $loggerFactory,
        ValidationRules $rules,
        I18n $i18n
    ) {
        parent::__construct($i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('password_reset_controller.log')
            ->createInstance('password_reset_controller');
        $this->rules = $rules;
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = array_clean($request->getQueryParams(), [
            'email',
            'code',
        ]);
        if (
            !$this->reminderCodeExists(User::whereEmail($email = $params['email'] ?? null)
                ->first(), $code = $params['code'] ?? null)
        ) {
            $this->flash->addMessage('status', __f('Invalid reset code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('home'));
        }

        return $this->view->render($response, 'pages/auth/password/reset.twig', compact('email', 'code'));
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
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
            $this->rules->confirmPassword()
        ));

        $params = array_clean($data, [
            'email',
            'code',
            'password',
        ]);
        if (!$this->reminderCodeExists($user = User::whereEmail($params['email'])->first(), $code = $params['code'])) {
            $clean = array_clean($data, [
                'email',
                'code',
            ]);
            // file deepcode ignore PrivacyLeak: password is removed by array_clean
            $this->logger->error('Invalid reset code', $clean);
            $this->flash->addMessage('status', __f('Invalid reset code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('home'));
        }

        Sentinel::getReminderRepository()->complete($user, $code, $params['password']);
        $this->flash->addMessage('status', __f('Your password has been reset and you can now sign in.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
    }

    /**
     * @param null|User   $user The user to validate
     * @param null|string $code The code to validate
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
