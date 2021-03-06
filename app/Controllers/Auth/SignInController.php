<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Exceptions\ValidationException;
use App\Factory\LoggerFactory;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;
use Odan\Session\PhpSession;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SignInController.
 */
class SignInController extends Controller
{
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected LoggerInterface       $logger;
    protected ValidationRules       $rules;
    protected PhpSession            $session;

    /**
     * SignInController constructor.
     *
     * @param Twig                 $view          The view
     * @param Messages             $flash         The flash
     * @param RouteParserInterface $routeParser   The routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param PhpSession           $session
     *
     * @throws Exception
     */
    public function __construct(Twig $view, Messages $flash, RouteParserInterface $routeParser, LoggerFactory $loggerFactory, ValidationRules $rules, PhpSession $session)
    {
        parent::__construct($session);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('signin_controller.log')->createInstance('signin_controller');
        $this->rules       = $rules;
        $this->session->set('current_url', 'auth.signin');
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->view->render($response, 'pages/auth/signin.twig', [
            'redirect' => $request->getQueryParams()['redirect'] ?? null,
        ]);
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function signin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = $this->validate($request, array_merge_recursive($this->rules->email(), $this->rules->required('password')));

        try {
            if (!Sentinel::authenticate(array_clean($data, [
                'email',
                'password',
            ]), isset($data['persist']))) {
                throw new Exception(_f('Incorrect email or password'));
            }
        } catch (Exception $e) {
            $this->flash->addMessage('status', $e->getMessage());
            $this->logger->error($e->getMessage(), array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));

            return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
        }

        return $response->withHeader('Location', $data['redirect'] ? $data['redirect'] : $this->routeParser->urlFor('home'));
    }
}
