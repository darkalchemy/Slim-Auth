<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Odan\Session\PhpSession;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;
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
    protected PhpSession           $session;

    /**
     * AccountPasswordController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param ValidationRules      $rules
     * @param PhpSession           $session
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        ValidationRules $rules,
        PhpSession $session
    ) {
        parent::__construct($session);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->rules       = $rules;
    }

    /**
     * @param Response $response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        return $this->view->render($response, 'pages/account/password.twig');
    }

    /**
     * @param Request $request
     * @param Response      $response
     *
     * @throws ValidationException
     *
     * @return Response
     */
    public function action(Request $request, Response $response): Response
    {
        $data = $this->validate($request, array_merge_recursive(
            $this->rules->password(),
            $this->rules->current_password(),
            $this->rules->confirm_password()
        ));
        Sentinel::getUserRepository()->update(Sentinel::check(), array_clean($data, ['password']));
        $this->flash->addMessage('status', _f('Password updated!'));

        return $response->withHeader('Location', $this->routeParser->urlFor('account.password'));
    }
}
