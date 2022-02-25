<?php

declare(strict_types=1);

namespace App\View;

use Slim\Csrf\Guard;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * CsrfRuntime class.
 */
class CsrfRuntime implements RuntimeExtensionInterface
{
    /**
     * @var Guard
     */
    protected Guard $csrf;

    /**
     * @param Guard $csrf The csrf
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
            <input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' .
            $this->csrf->getTokenName() . '">
            <input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' .
            $this->csrf->getTokenValue() . '">';
    }
}
