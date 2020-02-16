<?php

namespace App\Controllers\Auth\Password;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Models\User;
use App\Providers\SendMail;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PasswordRecoverController
 *
 * @package App\Controllers\Auth\Password
 */
class PasswordRecoverController extends Controller
{
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected SendMail              $sendMail;
    protected ValidationRules       $rules;

    /**
     * PasswordRecoverController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param SendMail             $sendMail
     * @param ValidationRules      $rules
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, SendMail $sendMail, ValidationRules $rules)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
        $this->sendMail = $sendMail;
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
        return $this->view->render($response, 'pages/auth/password/recover.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ValidationException
     * @throws Exception
     */
    public function recover(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, $this->rules->email());

        $params = array_clean($data, ['email']);
        if ($user = User::whereEmail($params['email'])->first()) {
            $reminder = Sentinel::getReminderRepository()->create($user);

            $this->sendMail->addRecipient($user->email, $user->username);
            $this->sendMail->setSubject(_f('Reset your password'));
            $this->sendMail->setMessage($this->view->fetch('email/auth/password/recover.twig', [
                'user' => $user,
                'code' => $reminder->code,
            ]));
            $this->sendMail->store();
        }
        $this->flash->addMessage('status', _f('An email has to been sent with instructions to reset your password.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.password.recover'));
    }
}
