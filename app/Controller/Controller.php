<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use Delight\I18n\I18n;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

/**
 * Class Controller.
 */
class Controller
{
    protected I18n $i18n;
    protected string $locale;

    /**
     * Controller constructor.
     *
     * @param I18n $i18n
     */
    public function __construct(I18n $i18n)
    {
        $this->i18n    = $i18n;
        $this->locale  = $this->i18n->getLocale() ?? $this->i18n->getSupportedLocales()[0];
    }

    /**
     * @param ServerRequestInterface $request The request
     * @param array                  $rules   The rules to process
     *
     * @throws ValidationException
     *
     * @return array
     */
    public function validate(ServerRequestInterface $request, array $rules = []): array
    {
        Validator::langDir(VENDOR_DIR . 'vlucas/valitron/lang/');
        Validator::lang(substr($this->locale, 0, 2));
        $params    = (array) $request->getParsedBody();
        $validator = new Validator($params);
        $validator->mapFieldsRules($rules);
        if (!$validator->validate()) {
            throw new ValidationException((array) $validator->errors(), $request->getServerParams()['HTTP_REFERER']);
        }

        return $params;
    }
}
