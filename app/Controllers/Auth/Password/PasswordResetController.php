<?php

declare(strict_types=1);

namespace App\Controllers\Auth\Password;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Factory\LoggerFactory;
use App\Models\User;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
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
 * Class PasswordResetController
 *
 * @package App\Controllers\Auth\Password
 */
class PasswordResetController extends Controller
{
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected LoggerInterface       $logger;
    protected ValidationRules       $rules;

    /**
     * PasswordResetController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, LoggerFactory $loggerFactory, ValidationRules $rules)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
        $this->logger = $loggerFactory->addFileHandler('password_reset_controller.log')->createInstance('password_reset_controller');
        $this->rules = $rules;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        $params = array_clean($request->getQueryParams(), [
            'email',
            'code',
        ]);
        if (!$this->reminderCodeExists(User::whereEmail($email = $params['email'] ?? null)->first(), $code = $params['code'] ?? null)) {
            $this->flash->addMessage('status', _f('Invalid reset code.'));

            return $response->withHeader('Location', $this->routeParser->urlFor('home'));
        }

        return $this->view->render($response, 'pages/auth/password/reset.twig', compact('email', 'code'));
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     * @throws ValidationException
     */
    public function reset(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, array_merge_recursive($this->rules->required('email'), $this->rules->required('code'), $this->rules->password(), $this->rules->confirm_password()));

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
     * @param User|null   $user
     * @param string|null $code
     *
     * @return bool
     */
    protected function reminderCodeExists(?User $user, ?string $code)
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
