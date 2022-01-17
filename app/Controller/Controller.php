<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use Odan\Session\PhpSession;
use Psr\Http\Message\ServerRequestInterface;
use Valitron\Validator;

/**
 * Class Controller.
 */
class Controller
{
    protected PhpSession $session;
    protected string $locale;

    /**
     * Controller constructor.
     *
     * @param PhpSession $session
     */
    public function __construct(PhpSession $session)
    {
        $this->session    = $session;
        $this->locale     = $this->session->get('locale') ?? 'en';
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
        Validator::langDir(VENDOR_DIR . 'vlucas/valitron/lang/');
        Validator::lang($this->locale);
        $validator = new Validator($params = $request->getParsedBody());
        $validator->mapFieldsRules($rules);
        if (!$validator->validate()) {
            throw new ValidationException($validator->errors(), $request->getServerParams()['HTTP_REFERER']);
        }

        return $params;
    }
}
