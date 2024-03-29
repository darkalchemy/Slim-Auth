<?php

declare(strict_types=1);

use Delight\I18n\I18n;

$container = (require __DIR__ . '/../bootstrap/app.php')->getContainer();

$processes = [
    'compile',
    'translate',
    'translate-all',
    'clear_cache',
];

$languages = [];
$locales   = $container->get(I18n::class)->getSupportedLocales();
foreach ($locales as $locale) {
    $languages[] = str_replace('-', '_', $locale);
}
$process = 'compile';
$lang    = 'en_US';
foreach ($argv as $arg) {
    if (in_array($arg, $processes)) {
        $process = $arg;
    } elseif (in_array($arg, $languages)) {
        $lang = $arg;
    }
}

switch ($process) {
    case 'translate-all':
        foreach ($languages as $locale) {
            translate($locale);
        }

        break;

    case 'translate':
        translate($lang);

        break;

    case 'clear_cache':
        remove_cached_files($container);

        break;

    default:
        compile_twig_templates($container);

        break;
}
