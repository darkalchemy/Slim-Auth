<?php

declare(strict_types=1);

namespace App\Controller\Auth\Password;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Model\User;
use App\Provider\StoreMail;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Delight\I18n\I18n;
use Odan\Session\SessionInterface;
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
    protected Twig $view;
    protected Messages $flash;
    protected RouteParserInterface $routeParser;
    protected ValidationRules $rules;
    protected StoreMail $storeMail;
    protected SessionInterface $session;
    protected I18n $i18n;

    /**
     * PasswordRecoverController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param StoreMail            $storeMail
     * @param ValidationRules      $rules
     * @param SessionInterface     $session
     * @param I18n                 $i18n
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        StoreMail $storeMail,
        ValidationRules $rules,
        SessionInterface $session,
        I18n $i18n
    ) {
        parent::__construct($session, $i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->storeMail   = $storeMail;
        $this->rules       = $rules;
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws SyntaxError
     * @throws LoaderError
     * @throws RuntimeError
     *
     * @return ResponseInterface
     */
    public function index(ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'pages/auth/password/recover.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws SyntaxError
     * @throws ValidationException
     * @throws LoaderError
     * @throws RuntimeError
     *
     * @return ResponseInterface
     */
    public function recover(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $this->validate($request, $this->rules->email());

        $params = array_clean($data, ['email']);
        if ($user = User::whereEmail($params['email'])->first()) {
            $reminder = Sentinel::getReminderRepository()->create($user);
            $this->storeMail->setUserID($user->id);
            $this->storeMail->setSubject(_f('Reset your password.'));
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
