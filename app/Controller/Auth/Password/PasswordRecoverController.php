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
     * @var ValidationRules
     */
    protected ValidationRules $rules;

    /**
     * @var StoreMail
     */
    protected StoreMail $storeMail;

    /**
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * @param Twig                 $view        The view
     * @param Messages             $flash       The flash
     * @param RouteParserInterface $routeParser The routeParser
     * @param StoreMail            $storeMail   The storeMail
     * @param ValidationRules      $rules       The rules
     * @param I18n                 $i18n        The i18n
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        StoreMail $storeMail,
        ValidationRules $rules,
        I18n $i18n
    ) {
        parent::__construct($i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->storeMail   = $storeMail;
        $this->rules       = $rules;
    }

    /**
     * @param ResponseInterface $response The response
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     *
     * @return ResponseInterface
     */
    public function index(ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'pages/auth/password/recover.twig');
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ValidationException
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
            $this->storeMail->setSubject(__f('Reset your password.'));
            $this->storeMail->setUri((string) $request->getUri(), '/auth/password/reset?');
            $this->storeMail->store();
        }
        $this->flash->addMessage('status', __f('An email has to been sent with instructions to reset your password.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('auth.password.recover'));
    }
}
