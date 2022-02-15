<?php

declare(strict_types=1);

use App\Model\User;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use DI\NotFoundException;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Slim\App;
use Valitron\Validator;

return function (App $app) {
    if (!($container = $app->getContainer())) {
        throw new NotFoundException('Could not get the container.');
    }

    Validator::addRule('emailIsUnique', function ($field, $value, array $params, array $fields) {
        if (!empty($value)) {
            $user = User::where($field, $value);
            if (Sentinel::check()) {
                $user = $user->where($field, '!=', Sentinel::check()->email);
            }
            $user = $user->first();
        }
        if (!empty($user)) {
            return false;
        }

        return true;
    }, 'is already in use');

    Validator::addRule('usernameIsUnique', function ($field, $value, array $params, array $fields) {
        if (!empty($value)) {
            $user = User::where($field, $value);
            if (Sentinel::check()) {
                $user = $user->where($field, '!=', Sentinel::check()->username);
            }
            $user = $user->first();
        }
        if (!empty($user)) {
            return false;
        }

        return true;
    }, 'is already in use');

    Validator::addRule('currentPassword', function ($field, $value, array $params, array $fields) {
        return Sentinel::getUserRepository()->validateCredentials(Sentinel::check(), [
            $field === 'current_password' ? 'password' : $field => $value,
        ]);
    }, 'is wrong');

    Validator::addRule('badWords', function ($field, $value, array $params, array $fields) use ($container) {
        $bad_words = $container->get('settings')['bad_words'];

        return !in_array(strtolower($value), $bad_words);
    }, 'is not allowed');

    Validator::addRule('isValidEmail', function ($field, $value, array $params, array $fields) use ($container) {
        $validator = $container->get(EmailValidator::class);

        return $validator->isValid($value, $container->get(RFCValidation::class));
    }, 'does not appear to be valid');
};
