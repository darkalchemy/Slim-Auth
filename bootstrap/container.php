<?php

declare(strict_types=1);

use App\Factory\LoggerFactory;
use App\Middleware\CheckSettingsMiddleware;
use App\View\CsrfExtension;
use App\View\TwigMessagesExtension;
use App\View\TwigUtilities;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Darkalchemy\Twig\TwigTranslationExtension;
use Delight\I18n\Codes;
use Delight\I18n\I18n;
use DI\Bridge\Slim\Bridge;
use Fullpipe\TwigWebpackExtension\WebpackExtension;
use Nyholm\Psr7\Factory\Psr17Factory;
use Odan\Session\Middleware\SessionMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use UMA\RedisSessionHandler;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return [
    Configuration::class => fn () => new Configuration(require CONFIG_DIR . 'settings.php'),

    App::class => function (ContainerInterface $container) {
        $app = Bridge::create($container);

        $config = $container->get(Configuration::class);
        $routeCacheFile = $config->findString('router.cache_file');
        if ($routeCacheFile) {
            $app->getRouteCollector()->setCacheFile($routeCacheFile);
        }
        (require BOOTSTRAP_DIR . 'middleware.php')($app);
        (require ROUTES_DIR . 'web.php')($app);
        (require BOOTSTRAP_DIR . 'exceptions.php')($app);
        (require BOOTSTRAP_DIR . 'validation.php')($app);

        return $app;
    },

    // replace default redis session handler
    RedisSessionHandler::class => function () {
        if (ini_get('session.save_handler') === 'redis') {
            session_set_save_handler(new RedisSessionHandler(), true);
        }
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->getArray('session');
        $session = new PhpSession();
        $session->setOptions((array) $settings);

        return $session;
    },

    SessionMiddleware::class => function (ContainerInterface $container) {
        return new SessionMiddleware($container->get(SessionInterface::class));
    },

    I18n::class => function () {
        $i18n = new I18n([
            Codes::EN_US,
            Codes::FR_FR,
        ]);
        $i18n->setSessionField('locale');
        $i18n->setDirectory(LOCALE_DIR);
        $i18n->setModule('messages');
        $i18n->setLocaleAutomatically();

        return $i18n;
    },

    RouteParserInterface::class => fn (ContainerInterface $container) => $container->get(App::class)->getRouteCollector()->getRouteParser(),

    ResponseInterface::class => fn (ContainerInterface $container) => $container->get(App::class)->getResponseFactory(),

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->all();
        $twig = Twig::create($settings['twig']['path'], [
            'cache' => $settings['twig']['cache'] ?? false,
        ]);
        $twig->addExtension(new WebpackExtension($settings['webpack']['manifest'], PUBLIC_DIR));
        $twig->addExtension(new TwigUtilities());
        $twig->addExtension(new CsrfExtension($container->get(Guard::class)));
        $twig->addExtension(new TwigMessagesExtension($container->get(Messages::class)));
        $twig->addExtension(new TwigTranslationExtension($container->get(I18n::class)));
        $twig->getEnvironment()->addGlobal('user', Sentinel::check());
        $twig->getEnvironment()->addGlobal('settings', $settings);
        $twig->getEnvironment()->addGlobal('errors', $container->get(Messages::class)->getFirstMessage('errors'));
        $twig->getEnvironment()->addGlobal('old', $container->get(Messages::class)->getFirstMessage('old'));

        return $twig;
    },

    Guard::class => function (ContainerInterface $container) {
        $storage = null;

        return new Guard($container->get(ResponseInterface::class), 'csrf', $storage, null, 200, 32, true);
    },

    PHPMailer::class => function (ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->getArray('mail');
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'];
        $mail->SMTPAuth = $settings['smtp_auth'];
        $mail->Username = $settings['smtp_username'];
        $mail->Password = $settings['smtp_password'];
        $mail->SMTPSecure = $settings['smtp_secure'];
        $mail->Port = $settings['smtp_port'];
        $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_user']);
        $mail->isHTML();

        return $mail;
    },

    WhoopsMiddleware::class => function () {
        return new WhoopsMiddleware([
            'enable' => ENV === 'DEVELOPMENT',
        ]);
    },

    LoggerFactory::class => fn (ContainerInterface $container) => new LoggerFactory($container->get(Configuration::class)->getArray('logger')),

    CheckSettingsMiddleware::class => fn (ContainerInterface $container) => new CheckSettingsMiddleware(
        $container->get(Configuration::class)->all(),
        $container->get(LoggerFactory::class),
        $container->get(Messages::class)
    ),

    'view' => DI\get(Twig::class),
];
