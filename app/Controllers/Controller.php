<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use Odan\Session\PhpSession;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

/**
 * Class Controller.
 */
class Controller
{
    protected PhpSession $phpSession;
    protected string $locale;

    public function __construct(PhpSession $phpSession)
    {
        $this->phpSession = $phpSession;
        $this->locale     = $this->phpSession->get('locale') ?? 'en';
    }

    /**
     * @param ServerRequestInterface $request The request
     * @param array                  $rules   The rules to process
     *
     * @throws ValidationException
     *
     * @return null|array|object
     */
    public function validate(ServerRequestInterface $request, array $rules = [])
    {
        Validator::langDir(__DIR__ . '/../../vendor/vlucas/valitron/lang/');
        Validator::lang($this->locale);
        $validator = new Validator($params = $request->getParsedBody());
        Validator::langDir(__DIR__ . '/../../vendor/vlucas/valitron/lang/');
        Validator::lang('fr');
        $validator->mapFieldsRules($rules);
        if (!$validator->validate()) {
            throw new ValidationException($validator->errors(), $request->getServerParams()['HTTP_REFERER']);
        }

        return $params;
    }
}
