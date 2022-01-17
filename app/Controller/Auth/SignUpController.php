<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Exception\ValidationException;
use App\Factory\LoggerFactory;
use App\Provider\StoreMail;
use App\Validation\ValidationRules;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Exception;
use Odan\Session\PhpSession;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;
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
    protected Twig                  $view;
    protected Messages              $flash;
    protected RouteParserInterface  $routeParser;
    protected LoggerInterface       $logger;
    protected ValidationRules       $rules;
    protected StoreMail             $storeMail;
    protected PhpSession            $session;

    /**
     * SignUpController constructor.
     *
     * @param Twig                 $view
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param LoggerFactory        $loggerFactory
     * @param ValidationRules      $rules
     * @param StoreMail            $storeMail
     * @param PhpSession           $session
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
        PhpSession $session
    ) {
        parent::__construct($session);
        $this->view        = $view;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->logger      = $loggerFactory->addFileHandler('signup_controller.log')
            ->createInstance('signup_controller');
        $this->rules       = $rules;
        $this->storeMail   = $storeMail;
        $this->session->set('current_url', 'auth.signup');
    }

    /**
     * @param Response $response The response
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        return $this->view->render($response, 'pages/auth/signup.twig');
    }

    /**
     * @param Request $request  The request
     * @param Response      $response The response
     *
     * @throws ValidationException
     *
     * @return Response
     */
    public function signup(Request $request, Response $response): Response
    {
        $data = $this->validate($request, array_merge_recursive(
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
            $this->storeMail->setSubject(_f('Confirm your email'));
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
