<?php

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AccountController
 *
 * @package App\Controllers\Account
 */
class AccountController extends Controller
{
    protected Twig                 $view;
    protected Messages             $flash;
    protected RouteParserInterface $routeParser;
    protected ValidationRules      $rules;
    /**
     * AccountController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     */
    /**
     * AccountController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param ValidationRules      $rules
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, ValidationRules $rules)
    {
        $this->view = $view;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
        $this->rules = $rules;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/account/index.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     * @throws ValidationException
     */
    public function action(ServerRequestInterface $request, ResponseInterface $response)
    {
        $rules = array_merge_recursive($this->rules->email(), $this->rules->required('username'));
        $data = $this->validate($request, $rules);

        Sentinel::check()
            ->update(array_clean($data, [
                'email',
                'first_name',
                'last_name',
            ]));

        $this->flash->addMessage('status', 'Account details updated!');

        return $response->withHeader('Location', $this->routeParser->urlFor('account'));
    }
}
