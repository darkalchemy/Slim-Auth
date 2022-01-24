<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use App\Provider\StoreMail;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Delight\I18n\I18n;
use Exception;
use Monolog\Logger;
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
 * Class SignUpController.
 */
class SignUpController extends Controller
{
    protected Twig $view;
    protected Messages $flash;
    protected RouteParserInterface $routeParser;
    protected LoggerInterface $logger;
    protected ValidationRules $rules;
    protected StoreMail $storeMail;
    protected SessionInterface $session;
    protected I18n $i18n;

    /**
     * SignUpController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param StoreMail            $storeMail
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
        StoreMail $storeMail,
        SessionInterface $session,
        I18n $i18n
    ) {
        parent::__construct($session, $i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('signup_controller.log', Logger::DEBUG)
            ->createInstance('signup_controller');
        $this->rules     = $rules;
        $this->storeMail = $storeMail;
        $this->session->set('current_url', 'auth.signup');
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
        return $this->view->render($response, 'pages/auth/signup.twig');
    }

    /**
     * @param ServerRequestInterface $request  The request
     * @param ResponseInterface      $response The response
     *
     * @throws ValidationException
     *
     * @return ResponseInterface
     */
    public function signup(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $this->validate($request, array_merge_recursive(
            $this->rules->email(),
            $this->rules->email_unique(),
            $this->rules->username(),
            $this->rules->password(),
            $this->rules->password_different(),
            $this->rules->confirm_password(),
            $this->rules->confirm_password_different()
        ));

        try {
            $user = Sentinel::register(array_clean($data, [
                'email',
                'username',
                'password',
            ]));
            $activation = Sentinel::getActivationRepository()->create($user);
            $this->storeMail->setUserID($user->id);
            $this->storeMail->setSubject(_f('Confirm your email.'));
            $this->storeMail->setBody($this->view->fetch('email/auth/password/activate.twig', [
                'user' => $user,
                'code' => $activation->code,
            ]));
            $this->storeMail->store();
        } catch (Exception $e) {
            $this->flash->addMessage('status', _f('Something went wrong'));
            $this->logger->error($e->getMessage(), array_clean($data, [
                'email',
                'username',
                'persist',
                'csrf_name',
                'csrf_value',
            ]));

            return $response->withHeader('Location', $this->routeParser->urlFor('auth.signup'));
        }

        $this->logger->info('Signup Successful', array_clean($data, [
            'email',
            'username',
        ]));
        $this->flash->addMessage('success', _f('Signup successful. Please check and confirm your email before continuing.'));

        return $response->withHeader('Location', $this->routeParser->urlFor('home'));
    }
}
