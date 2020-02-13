<?php

declare(strict_types = 1);

use App\Models\User;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Selective\Config\Configuration;
use Valitron\Validator;

Validator::addRule('emailIsUnique', function ($field, $value, $params, $fields) {
    if (!empty($value)) {
        $user = User::where('email', $value);
        if (Sentinel::check()) {
            $user = $user->where('email', '!=', Sentinel::check()->email);
        }
        $user = $user->first();
    }
    if (!empty($user)) {
        return false;
    }

    return true;
}, 'is already in use');

Validator::addRule('usernameIsUnique', function ($field, $value, $params, $fields) {
    if (!empty($value)) {
        $user = User::where('username', $value);
        if (Sentinel::check()) {
            $user = $user->where('username', '!=', Sentinel::check()->username);
        }
        $user = $user->first();
    }
    if (!empty($user)) {
        return false;
    }

    return true;
}, 'is already in use');

Validator::addRule('currentPassword', function ($field, $value, $params, $fields) {
    return Sentinel::getUserRepository()
        ->validateCredentials(Sentinel::check(), ['password' => $value]);
}, 'is wrong');

Validator::addRule('badWords', function ($field, $value, $params, $fields) use ($container) {
    $bad_words = $container->get(Configuration::class)
        ->getArray('bad_words');

    return !in_array(strtolower($value), $bad_words);
}, 'is not allowed');

Validator::addRule('isValidEmail', function ($field, $value, $params, $fields) use ($container) {
    $validator = $container->get(EmailValidator::class);

    return $validator->isValid($value, $container->get(RFCValidation::class));
}, 'does not appear to be valid');
