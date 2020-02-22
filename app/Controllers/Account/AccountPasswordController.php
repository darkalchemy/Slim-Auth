<?php

declare(strict_types=1);

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
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
 * Class AccountPasswordController.
 */
class AccountPasswordController extends Controller
{
    protected Twig                 $view;
    protected Messages             $flash;
    protected RouteParserInterface $routeParser;
    protected ValidationRules      $rules;
    protected PhpSession $phpSession;

    /**
     * AccountPasswordController constructor.
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, ValidationRules $rules, PhpSession $phpSession)
    {
        parent::__construct($phpSession);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->rules       = $rules;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function index(ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/account/password.twig');
    }

    /**
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function action(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, array_merge_recursive($this->rules->password(), $this->rules->current_password(), $this->rules->confirm_password()));
        Sentinel::getUserRepository()->update(Sentinel::check(), array_clean($data, ['password']));
        $this->flash->addMessage('status', _f('Password updated!'));

        return $response->withHeader('Location', $this->routeParser->urlFor('account.password'));
    }
}
