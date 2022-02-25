<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Class ValidationRules.
 */
class ValidationRules
{
    /**
     * @return array
     */
    public function password(): array
    {
        return [
            'password' => [
                'required',
                [
                    'lengthMin',
                    12,
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
    public function passwordDifferent(): array
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
    public function currentPassword(): array
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
    public function confirmPassword(): array
    {
        return [
            'confirm_password' => [
                'required',
                [
                    'lengthMin',
                    12,
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
    public function confirmPasswordDifferent(): array
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
    public function email(): array
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
    public function emailIsUnique(): array
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
    public function username(): array
    {
        return [
            'username' => [
                'required',
                'usernameIsUnique',
                [
                    'lengthMin',
                    5,
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
     * @param string $key The key
     *
     * @return array
     */
    public function required(string $key): array
    {
        return [
            $key => [
                'required',
            ],
        ];
    }
}
