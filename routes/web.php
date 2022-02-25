<?php

declare(strict_types=1);

use App\Controller\Account\AccountController;
use App\Controller\Account\AccountPasswordController;
use App\Controller\Auth\Password\PasswordRecoverController;
use App\Controller\Auth\Password\PasswordResetController;
use App\Controller\Auth\SignInController;
use App\Controller\Auth\SignOutController;
use App\Controller\Auth\SignUpController;
use App\Controller\Auth\UserActivateController;
use App\Controller\Dashboard\DashboardController;
use App\Controller\HomeController;
use App\Controller\LocaleController;
use App\Middleware\RedirectIfAuthenticated;
use App\Middleware\RedirectIfNotAuthenticated;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Csrf\Guard;

return function (App $app) {
    $app->get('/clear', function (ResponseInterface $response) {
        $response->getBody()->write((string) apcu_clear_cache());

        return $response;
    });

    $app->get('/apcu', function (ResponseInterface $response) {
        $response->getBody()->write((string) json_encode(apcu_cache_info(), \JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/session', function (ResponseInterface $response) {
        $response->getBody()->write((string) json_encode($_SESSION, \JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/version', function (ResponseInterface $response) {
        $response->getBody()->write((string) phpinfo());

        return $response;
    });

    $app->get('/', HomeController::class)->setName('home')->add(Guard::class);
    $app->get('/locale/{lang}', LocaleController::class)->setName('translate');
    $app->post('/signout', SignOutController::class)->setName('auth.signout')->add(Guard::class);

    $app->group('/auth', function ($route) {
        $route->group('', function ($route) {
            $route->get('/signin', [SignInController::class, 'index'])->setName('auth.signin');
            $route->post('/signin', [SignInController::class, 'signin']);
            $route->get('/signup', [SignUpController::class, 'index'])->setName('auth.signup');
            $route->post('/signup', [SignUpController::class, 'signup']);
            $route->get('/activate', UserActivateController::class)->setName('auth.activate');
        });

        $route->group('/password', function ($route) {
            $route->get('/recover', [PasswordRecoverController::class, 'index'])->setName('auth.password.recover');
            $route->post('/recover', [PasswordRecoverController::class, 'recover']);
            $route->get('/reset', [PasswordResetController::class, 'index'])->setName('auth.password.reset');
            $route->post('/reset', [PasswordResetController::class, 'reset']);
        });
    })->add(RedirectIfAuthenticated::class);

    $app->group('', function ($route) {
        $route->get('/account/account', [AccountController::class, 'index'])->setName('account.account');
        $route->post('/account/account', [AccountController::class, 'action']);
        $route->get('/account/password', [AccountPasswordController::class, 'index'])->setName('account.password');
        $route->post('/account/password', [AccountPasswordController::class, 'action']);
        $route->get('/dashboard', DashboardController::class)->setName('dashboard');
    })->add(RedirectIfNotAuthenticated::class)->add(Guard::class);
};
