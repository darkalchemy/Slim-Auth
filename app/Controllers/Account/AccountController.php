<?php

declare(strict_types=1);

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
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
    protected Twig                 $view;
    protected Messages             $flash;
    protected RouteParserInterface $routeParser;
    protected ValidationRules      $rules;

    /**
     * AccountController constructor.
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, ValidationRules $rules)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
        $this->rules = $rules;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     *
     * @return ResponseInterface
     */
    public function index(ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/account/index.twig');
    }

    /**
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function action(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, array_merge_recursive($this->rules->email(), $this->rules->required('username')));
        Sentinel::check()->update(array_clean($data, [
            'email', 'username',
        ])
        );
        $this->flash->addMessage('status', _f('Account details updated!'));

        return $response->withHeader('Location', $this->routeParser->urlFor('account.account'));
    }
}
