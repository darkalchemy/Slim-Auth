<?php

declare(strict_types=1);

namespace App\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * TwigPhpExtension class.
 */
class TwigPhpExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private array $functions = [
        'get_included_files',
        'human_readable_size',
        'memory_get_peak_usage',
        'hrtime',
    ];

    /**
     * @param array $functions The functions
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
     * @param array $functions The functions
     */
    public function allowFunctions(array $functions): void
    {
        $this->functions = $functions;
    }
}
