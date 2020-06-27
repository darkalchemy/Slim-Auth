<?php

declare(strict_types=1);

use Delight\I18n\I18n;
use Selective\Config\Configuration;

$container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();

$root_path = $container->get(Configuration::class)->findString('root');
$processes = [
    'compile',
    'translate',
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
    case 'translate':
        copy($root_path . '/bin/i18n.sh', $root_path . '/i18n.sh');
        chmod($root_path . '/i18n.sh', 0775);
        passthru(sprintf('./i18n.sh %s', $lang));
        unlink($root_path . '/i18n.sh');

        break;
    case 'clear_cache':
        remove_cached_files($container);

        break;
    default:
        compile_twig_templates($container);

        break;
}
