<?php

declare(strict_types=1);

namespace App\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigUtilities extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('runtime', [$this, 'runtime']),
            new TwigFunction('max_mem_usage', [$this, 'max_mem_usage']),
        ];
    }

    /**
     * @param bool $real
     * @return string
     */
    public function max_mem_usage(bool $real): string
    {
        return human_readable_size(memory_get_peak_usage($real), 2);
    }

    /**
     * @return float|int
     */
    public function runtime()
    {
        global $startTime;

        return round(microtime(true) - $startTime, 5) * 1000;
    }
}
