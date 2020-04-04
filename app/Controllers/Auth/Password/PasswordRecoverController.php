<?php

declare(strict_types=1);

namespace App\Controllers\Auth\Password;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Models\User;
use App\Providers\StoreMail;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Odan\Session\PhpSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PasswordRecoverController.
 */
class PasswordRecoverController extends Controller
{
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected ValidationRules       $rules;
    protected StoreMail             $storeMail;
    protected PhpSession            $session;

    /**
     * PasswordRecoverController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param StoreMail            $storeMail
     * @param ValidationRules      $rules
     * @param PhpSession           $session
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, StoreMail $storeMail, ValidationRules $rules, PhpSession $session)
    {
        parent::__construct($session);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->storeMail   = $storeMail;
        $this->rules       = $rules;
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
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/auth/password/recover.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function recover(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, $this->rules->email());

        $params = array_clean($data, ['email']);
        if ($user = User::whereEmail($params['email'])->first()) {
            $reminder = Sentinel::getReminderRepository()->create($user);
            $this->storeMail->setUserID($user->id);
            $this->storeMail->setSubject(_f('Reset your password'));
            $this->storeMail->setBody($this->view->fetch('email/auth/password/recover.twig', [
                'user' => $user,
                'code' => $reminder->code,
            ]));
            $this->storeMail->store();
        }
        $this->flash->addMessage('status', _f('An email has to been sent with instructions to reset your password.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.password.recover'));
    }
}
