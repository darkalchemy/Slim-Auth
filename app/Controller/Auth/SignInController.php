<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Exception\AuthException;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Delight\I18n\I18n;
use Exception;
use Odan\Session\SessionInterface;
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
    protected Twig $view;
    protected Messages $flash;
    protected RouteParserInterface $routeParser;
    protected LoggerInterface $logger;
    protected ValidationRules $rules;
    protected SessionInterface $session;
    protected I18n $i18n;

    /**
     * SignInController constructor.
     *
     * @param Twig                 $view          The view
     * @param Messages             $flash         The flash
     * @param RouteParserInterface $routeParser   The routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param SessionInterface     $session
     * @param I18n                 $i18n
     *
     * @throws Exception
     */
    public function __construct(
        Twig $view,
        Messages $flash,
        RouteParserInterface $routeParser,
        LoggerFactory $loggerFactory,
        ValidationRules $rules,
        SessionInterface $session,
        I18n $i18n
    ) {
        parent::__construct($i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('signin_controller.log')
            ->createInstance('signin_controller');
        $this->rules   = $rules;
        $this->session = $session;
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
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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
    public function signin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $this->validate($request, array_merge_recursive(
            $this->rules->email(),
            $this->rules->required('password')
        ));

        try {
            if (!Sentinel::authenticate(array_clean($data, [
                'email',
                'password',
            ]), isset($data['persist']))) {
                throw new AuthException('Incorrect email or password.');
            }
        } catch (Exception $e) {
            $this->flash->addMessage('status', $e->getMessage());
            $this->logger->error($e->getMessage(), array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));

            return $response->withHeader('Location', $this->routeParser->urlFor('auth.signin'));
        }

        return $response->withHeader('Location', $data['redirect'] ?: $this->routeParser->urlFor('home'));
    }
}
