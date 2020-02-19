<?php

declare(strict_types=1);

use Selective\Config\Configuration;

$container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();

$root_path = $container->get(Configuration::class)->findString('root');
$processes = [
    'compile',
    'translate',
];
$languages = $container->get(Configuration::class)->getArray('lang');
$process   = 'compile';
$lang      = 'en_US';
foreach ($argv as $arg) {
    if (in_array($arg, $processes)) {
        $process = $arg;
    } elseif (in_array($arg, $languages)) {
        $lang = $arg;
    }
}

switch ($process) {
    case 'compile':
        compile_twig_templates($container);

        break;
    case 'translate':
        copy($root_path . '/bin/i18n.sh', $root_path . '/i18n.sh');
        chmod($root_path . '/i18n.sh', 0755);
        passthru(sprintf('./i18n.sh %s', $lang));
        unlink($root_path . '/i18n.sh');

        break;
}
