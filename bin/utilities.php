<?php

declare(strict_types=1);

use Delight\I18n\I18n;

$container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();

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

function translate(string $lang): void
{
    copy(VENDOR_DIR . 'delight-im/i18n/i18n.sh', ROOT_DIR . 'i18n.sh');
    chmod(ROOT_DIR . 'i18n.sh', 0775);
    passthru("sed -i 's/\\-\\-keyword \\-\\-keyword/\\-\\-keyword \\-\\-keyword=\"translateFormatted:1\" \\-\\-keyword=\"translateFormattedExtended:1\" \\-\\-keyword=\"translatePlural:1,2,3t\" \\-\\-keyword=\"translatePluralFormatted:1,2\" \\-\\-keyword=\"translatePluralFormattedExtended:1,2\" \\-\\-keyword=\"translateWithContext:1,2c,2t\" \\-\\-keyword=\"markForTranslation:1,1t\" \\-\\-flag=\"translateFormatted:1:php\\-format\" \\-\\-flag=\"translateFormattedExtended:1:no\\-php\\-format\" \\-\\-flag=\"translatePluralFormatted:1:php\\-format\" \\-\\-flag=\"translatePluralFormattedExtended:1:no\\-php\\-format\" \\-\\-keyword/g' i18n.sh");
    passthru(sprintf('./i18n.sh %s', $lang));
    unlink(ROOT_DIR . 'i18n.sh');
}
