<?php

declare(strict_types=1);

namespace App\View;

use Slim\Csrf\Guard;
use Twig\Extension\RuntimeExtensionInterface;

class CsrfRuntime implements RuntimeExtensionInterface
{
    protected Guard $csrf;

    /**
     * CsrfRuntime constructor.
     *
     * @param Guard $csrf
     */
    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * @return string
     */
    public function csrf(): string
    {
        return '
            <input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' . $this->csrf->getTokenName() . '">
            <input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' . $this->csrf->getTokenValue() . '">';
    }
}
