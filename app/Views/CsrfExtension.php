<?php

declare(strict_types=1);

namespace App\Views;

use Slim\Csrf\Guard;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CsrfExtension
 *
 * @package App\Views
 */
class CsrfExtension extends AbstractExtension
{
    protected Guard $csrf;

    /**
     * CsrfExtension constructor.
     *
     * @param Guard $csrf
     */
    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf', [$this, 'csrf']),
        ];
    }

    /**
     * @return string
     */
    public function csrf()
    {
        return '
            <input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' . $this->csrf->getTokenName() . '">
            <input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' . $this->csrf->getTokenValue() . '">';
    }
}
