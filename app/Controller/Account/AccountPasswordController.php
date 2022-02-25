<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Controller\Controller;
use App\Exception\ValidationException;
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
 * Class AccountPasswordController.
 */
class AccountPasswordController extends Controller
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
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * @param Twig                 $view        The view
     * @param Messages             $flash       The flash
     * @param RouteParserInterface $routeParser The routeParser
     * @param ValidationRules      $rules       The rules
     * @param I18n                 $i18n        The i18n
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        ValidationRules $rules,
        I18n $i18n
    ) {
        parent::__construct($i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
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
        return $this->view->render($response, 'pages/account/password.twig');
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function action(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $this->validate($request, array_merge_recursive(
            $this->rules->password(),
            $this->rules->currentPassword(),
            $this->rules->confirmPassword()
        ));
        Sentinel::getUserRepository()->update(Sentinel::check(), array_clean($data, ['password']));
        $this->flash->addMessage('status', __f('Password updated!'));

        return $response->withHeader('Location', $this->routeParser->urlFor('account.password'));
    }
}
