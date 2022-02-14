<?php

declare(strict_types=1);

namespace App\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPhpExtension extends AbstractExtension
{
    private array $functions = [
        'uniqid',
        'floor',
        'ceil',
        'hash',
        'get_included_files',
        'count',
    ];

    /**
     * @param array $functions
     */
    public function __construct(array $functions = [])
    {
        if ($functions) {
            $this->allowFunctions($functions);
        }
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        $twigFunctions = [];

        foreach ($this->functions as $function) {
            $twigFunctions[] = new TwigFunction($function, $function);
        }

        return $twigFunctions;
    }

    /**
     * @param array $functions
     */
    public function allowFunctions(array $functions): void
    {
        $this->functions = $functions;
    }
}
