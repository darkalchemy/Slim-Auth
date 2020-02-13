<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

/**
 * Class Controller.
 */
class Controller
{
    /**
     * @param ServerRequestInterface $request The request
     * @param array                  $rules   The rules to process
     *
     * @return array|object|null
     * @throws ValidationException
     */
    public function validate(ServerRequestInterface $request, array $rules = [])
    {
        $validator = new Validator($params = $request->getParsedBody());
        $validator->mapFieldsRules($rules);
        if (!$validator->validate()) {
            throw new ValidationException($validator->errors(), $request->getServerParams()['HTTP_REFERER']);
        }

        return $params;
    }
}
