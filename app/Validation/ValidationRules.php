<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Class ValidationRules
 *
 * @package App\Validation
 */
class ValidationRules
{
    /**
     * @return array
     */
    public function password()
    {
        return [
            'password' => [
                'required',
                [
                    'lengthMin',
                    8,
                ],
                [
                    'equals',
                    'confirm_password',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function password_different()
    {
        return [
            'password' => [
                [
                    'different',
                    'username',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function current_password()
    {
        return [
            'current_password' => [
                'required',
                'currentPassword',
            ],
        ];
    }

    /**
     * @return array
     */
    public function confirm_password()
    {
        return [
            'confirm_password' => [
                'required',
                [
                    'lengthMin',
                    8,
                ],
                [
                    'equals',
                    'password',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function confirm_password_different()
    {
        return [
            'confirm_password' => [
                [
                    'different',
                    'username',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function email()
    {
        return [
            'email' => [
                'required',
                'email',
                'isValidEmail',
            ],
        ];
    }

    /**
     * @return array
     */
    public function email_unique()
    {
        return [
            'email' => [
                'emailIsUnique',
            ],
        ];
    }

    /**
     * @return array
     */
    public function username()
    {
        return [
            'username' => [
                'required',
                'usernameIsUnique',
                [
                    'lengthMin',
                    4,
                ],
                [
                    'regex',
                    '/^[\p{L}\p{M}\p{Pd}\p{Pc}\p{N}]*$/u',
                ],
                'badWords',
            ],
        ];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function required(string $key)
    {
        return [
            $key => [
                'required',
            ],
        ];
    }
}
