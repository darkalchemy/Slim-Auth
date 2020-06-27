<?php

declare(strict_types=1);

use App\Controllers\Account\AccountController;
use App\Controllers\Account\AccountPasswordController;
use App\Controllers\Auth\Password\PasswordRecoverController;
use App\Controllers\Auth\Password\PasswordResetController;
use App\Controllers\Auth\SignInController;
use App\Controllers\Auth\SignOutController;
use App\Controllers\Auth\SignUpController;
use App\Controllers\Auth\UserActivateController;
use App\Controllers\Dashboard\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\LocaleController;
use App\Middleware\RedirectIfAuthenticated;
use App\Middleware\RedirectIfGuest;
use Slim\App;

return function (App $app) {
    $app->get('/', HomeController::class)->setName('home');
    $app->get('/locale/{lang}', LocaleController::class)->setName('translate');

    $app->group('/auth', function ($route) {
        $route->group('', function ($route) {
            $route->get('/signin', [SignInController::class, 'index'])->setName('auth.signin');
            $route->post('/signin', [SignInController::class, 'signin']);
            $route->get('/signup', [SignUpController::class, 'index'])->setName('auth.signup');
            $route->post('/signup', [SignUpController::class, 'signup']);
            $route->get('/activate', UserActivateController::class)->setName('auth.activate');
        })->add(RedirectIfAuthenticated::class);

        $route->group('/password', function ($route) {
            $route->get('/recover', [PasswordRecoverController::class, 'index'])->setName('auth.password.recover');
            $route->post('/recover', [PasswordRecoverController::class, 'recover']);
            $route->get('/reset', [PasswordResetController::class, 'index'])->setName('auth.password.reset');
            $route->post('/reset', [PasswordResetController::class, 'reset']);
        })->add(RedirectIfAuthenticated::class);

        $route->post('/signout', SignOutController::class)->setName('auth.signout');
    });

    $app->group('', function ($route) {
        $route->get('/account/account', [AccountController::class, 'index'])->setName('account.account');
        $route->post('/account/account', [AccountController::class, 'action']);
        $route->get('/account/password', [AccountPasswordController::class, 'index'])->setName('account.password');
        $route->post('/account/password', [AccountPasswordController::class, 'action']);
        $route->get('/dashboard', DashboardController::class)->setName('dashboard');
    })->add(RedirectIfGuest::class);
};
