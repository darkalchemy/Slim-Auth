<?php

declare(strict_types=1);

use App\Controllers\LocaleController;
use App\Extensions\TwigTranslationExtension;
use App\Factory\LoggerFactory;
use App\Middleware\CheckSettingsMiddleware;
use App\Views\CsrfExtension;
use App\Views\TwigMessagesExtension;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Delight\I18n\Codes;
use Delight\I18n\I18n;
use DI\Bridge\Slim\Bridge;
use Fullpipe\TwigWebpackExtension\WebpackExtension;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Selective\Config\Configuration;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return [
    Configuration::class => function () {
        return new Configuration(require __DIR__ . '/../config/settings.php');
    },

    App::class => function (ContainerInterface $container) {
        $app = Bridge::create($container);

        $config = $container->get(Configuration::class);
        $routeCacheFile = $config->findString('router.cache_file');
        if ($routeCacheFile) {
            $app->getRouteCollector()->setCacheFile($routeCacheFile);
        }

        return $app;
    },

    SessionInterface::class => function (ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->getArray('session');
        $phpSession = new PhpSession();
        $phpSession->setOptions($settings);

        return $phpSession;
    },

    I18n::class => DI\factory(function () {
        return new I18n([
            Codes::EN_US,
            Codes::FR_FR,
        ]);
    }),

    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    ResponseInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    Twig::class => function (ContainerInterface $container) {
        $settings = $container->get(Configuration::class)->all();
        $twig = Twig::create($settings['twig']['path'], [
            'cache' => $settings['twig']['cache'] ?? false,
        ]);

        $twig->addExtension(new WebpackExtension($settings['webpack']['manifest'], $settings['webpack']['js_path'], $settings['webpack']['css_path']));
        $twig->addExtension(new CsrfExtension($container->get(Guard::class)));
        $twig->addExtension(new TwigMessagesExtension($container->get(Messages::class)));
        $twig->addExtension(new TwigTranslationExtension($container->get(I18n::class), $container->get(PhpSession::class)));
        $twig->getEnvironment()->addGlobal('user', Sentinel::check());
        $twig->getEnvironment()->addGlobal('settings', $settings);
        $twig->getEnvironment()->addGlobal('errors', $container->get(Messages::class)->getFirstMessage('errors'));
        $twig->getEnvironment()->addGlobal('old', $container->get(Messages::class)->getFirstMessage('old'));

        return $twig;
    },

    Guard::class => function (ContainerInterface $container) {
        return new Guard($container->get(ResponseInterface::class));
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

    WhoopsMiddleware::class => function (ContainerInterface $container) {
        $appEnv = $container->get(Configuration::class)->findString('site.app_env');

        return new WhoopsMiddleware([
            'enable' => $appEnv === 'DEVELOPMENT',
        ]);
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get(Configuration::class)->getArray('logger'));
    },

    CheckSettingsMiddleware::class => function (ContainerInterface $container) {
        return new CheckSettingsMiddleware($container->get(Configuration::class)->all(), $container->get(Messages::class));
    },

    LocaleController::class => function (ContainerInterface $container) {
        return new LocaleController($container->get(I18n::class), $container->get(Messages::class), $container->get(RouteParserInterface::class), $container->get(PhpSession::class));
    },

    'view' => DI\get(Twig::class),
];
