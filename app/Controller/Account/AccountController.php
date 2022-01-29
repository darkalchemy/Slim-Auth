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
 * Class AccountController.
 */
class AccountController extends Controller
{
    protected Twig $view;
    protected Messages $flash;
    protected RouteParserInterface $routeParser;
    protected ValidationRules $rules;
    protected I18n $i18n;

    /**
     * AccountController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param ValidationRules      $rules
     * @param I18n                 $i18n
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
        return $this->view->render($response, 'pages/account/index.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function action(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $this->validate($request, array_merge_recursive(
            $this->rules->email(),
            $this->rules->required('username')
        ));
        Sentinel::check()->update(array_clean($data, [
            'email', 'username',
        ]));
        $this->flash->addMessage('status', _f('Account details updated!'));

        return $response->withHeader('Location', $this->routeParser->urlFor('account.account'));
    }
}
