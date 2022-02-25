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
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ValidationRules
     */
    protected ValidationRules $rules;

    /**
     * @var StoreMail
     */
    protected StoreMail $storeMail;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * @param Twig                 $view          The view
     * @param Messages             $flash         The flash
     * @param RouteParserInterface $routeParser   The routeParser
     * @param LoggerFactory        $loggerFactory The loggerFactory
     * @param ValidationRules      $rules         The rules
     * @param StoreMail            $storeMail     The storeMail
     * @param SessionInterface     $session       The session
     * @param I18n                 $i18n          The i18n
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
        parent::__construct($i18n);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('signup_controller.log')
            ->createInstance('signup_controller');
        $this->rules     = $rules;
        $this->storeMail = $storeMail;
        $this->session   = $session;
        $this->session->set('current_url', 'auth.signup');
    }

    /**
     * @param ResponseInterface $response The response
     *
     * @throws SyntaxError
     * @throws LoaderError
     * @throws RuntimeError
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
            $this->rules->emailIsUnique(),
            $this->rules->username(),
            $this->rules->password(),
            $this->rules->passwordDifferent(),
            $this->rules->confirmPassword(),
            $this->rules->confirmPasswordDifferent()
        ));

        try {
            $user = Sentinel::register(array_clean($data, [
                'email',
                'username',
                'password',
            ]));
            $activation = Sentinel::getActivationRepository()->create($user);
            $this->storeMail->setUserID($user->id);
            $this->storeMail->setSubject(__f('Confirm your email.'));
            $this->storeMail->setUri((string) $request->getUri(), '/auth/activate?');
            $this->storeMail->store();
        } catch (Exception $e) {
            $this->flash->addMessage('status', __f('Something went wrong'));
            // file deepcode ignore PrivacyLeak: password is removed by array_clean
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
        $this->flash->addMessage(
            'success',
            __f('Signup successful. Please check and confirm your email before continuing.')
        );

        return $response->withHeader('Location', $this->routeParser->urlFor('home'));
    }
}
