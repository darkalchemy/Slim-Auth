<?php

declare(strict_types=1);

namespace App\Views;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigUtilities extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('runtime', [$this, 'runtime']),
            new TwigFunction('max_mem_usage', [$this, 'max_mem_usage']),
        ];
    }

    public function max_mem_usage(bool $real)
    {
        return human_readable_size(memory_get_peak_usage($real), 2);
    }

    public function runtime()
    {
        global $starttime;

        return round(microtime(true) - $starttime, 5) * 1000;
    }
}
