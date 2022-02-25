<?php

declare(strict_types=1);

use App\Factory\LoggerFactory;
use App\Middleware\CheckSettingsMiddleware;
use App\View\CsrfExtension;
use App\View\TwigMessagesExtension;
use App\View\TwigPhpExtension;
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
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Umpirsky\PermissionsHandler\ChmodPermissionsSetter;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

global $startTime;

return [
    'settings' => function () {
        return require CONFIG_DIR . 'settings.php';
    },

    App::class => function (ContainerInterface $container) {
        $app = Bridge::create($container);
        $settings = $container->get('settings');
        if ($settings['environment'] === 'PRODUCTION') {
            $routeCacheFile = $settings['router_cache_file'];
            $app->getRouteCollector()->setCacheFile($routeCacheFile);
        }

        (require BOOTSTRAP_DIR . 'middleware.php')($app);
        (require ROUTES_DIR . 'web.php')($app);
        (require BOOTSTRAP_DIR . 'exceptions.php')($app);
        (require BOOTSTRAP_DIR . 'validation.php')($app);

        return $app;
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $session = new PhpSession();
        $session->setOptions((array) $settings['session']);

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

    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    ResponseInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    Twig::class => function (ContainerInterface $container) use ($startTime) {
        $settings = $container->get('settings');
        $twig = Twig::create($settings['twig']['path'], [
            'cache' => $settings['environment'] === 'PRODUCTION' ? $settings['twig']['cache'] : false,
        ]);
        $twig->getEnvironment()->setCharset($settings['twig']['charset']);
        $twig->getEnvironment()->enableStrictVariables();
        $twig->addExtension(new WebpackExtension($settings['webpack']['manifest'], PUBLIC_DIR));
        $twig->addExtension($container->get(TwigPhpExtension::class));
        $twig->addExtension($container->get(CsrfExtension::class));
        $twig->addExtension($container->get(TwigMessagesExtension::class));
        $twig->addExtension($container->get(TwigTranslationExtension::class));
        $twig->getEnvironment()->addGlobal('user', Sentinel::check());
        $twig->getEnvironment()->addGlobal('settings', $settings);
        $twig->getEnvironment()->addGlobal('startTime', $startTime);

        return $twig;
    },

    Guard::class => function (ContainerInterface $container) {
        $storage = [];

        return new Guard($container->get(ResponseInterface::class), 'csrf', $storage, null, 200, 32, true);
    },

    PHPMailer::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['mail'];
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'];
        $mail->SMTPAuth = $settings['smtp_auth'];
        $mail->Port = $settings['smtp_port'];
        if ($settings['smtp_username']) {
            $mail->Username = $settings['smtp_username'];
        }
        if ($settings['smtp_password']) {
            $mail->Password = $settings['smtp_password'];
        }
        if ($settings['smtp_secure']) {
            $mail->SMTPSecure = $settings['smtp_secure'];
        }
        $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_user']);
        $mail->isHTML();

        return $mail;
    },

    WhoopsMiddleware::class => function (ContainerInterface $container) {
        $env = $container->get('settings')['environment'];

        return new WhoopsMiddleware([
            'enable' => $env === 'DEVELOPMENT',
        ]);
    },

    LoggerFactory::class => fn (ContainerInterface $container) => new LoggerFactory(
        $container->get('settings')['logger'],
        $container->get(ChmodPermissionsSetter::class)
    ),

    CheckSettingsMiddleware::class => fn (ContainerInterface $container) => new CheckSettingsMiddleware(
        $container->get('settings'),
        $container->get(LoggerFactory::class),
        $container->get(Messages::class),
        $container->get(ChmodPermissionsSetter::class)
    ),

    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class,
        );
    },
];
