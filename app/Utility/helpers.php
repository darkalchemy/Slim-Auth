<?php

declare(strict_types=1);

use Darkalchemy\Twig\TwigCompiler;
use Darkalchemy\Twig\TwigTranslationExtension;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Selective\Config\Configuration;
use Slim\Views\Twig;

/**
 * @param array $array
 * @param array $keys
 *
 * @return array
 */
function array_clean(array $array, array $keys): array
{
    return array_intersect_key($array, array_flip($keys));
}

/**
 * @param ContainerInterface $container
 *
 * @throws NotFoundExceptionInterface
 * @throws ContainerExceptionInterface
 * @throws Exception
 *
 * @return int
 */
function compile_twig_templates(ContainerInterface $container): int
{
    $settings    = $container->get(Configuration::class)->all();
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? VIEWS_DIR . 'cache/';
    $twig        = $container->get(Twig::class)->getEnvironment();
    $ext         = $container->get(TwigTranslationExtension::class);
    $compiler    = new TwigCompiler($twig, $ext, $cache, true);

    $compiler->compile();

    return 0;
}

/**
 * @param string $text
 * @param mixed  ...$replacements
 *
 * @return string
 */
function _f(string $text, ...$replacements): string
{
    global $i18n;

    return $i18n->translateFormatted($text, ...$replacements);
}

/**
 * @param string $text
 * @param mixed  ...$replacements
 *
 * @return string
 */
function _fe(string $text, ...$replacements): string
{
    global $i18n;

    return $i18n->translateFormattedExtended($text, ...$replacements);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 *
 * @return string
 */
function _p(string $text, string $alternative, int $count): string
{
    global $i18n;

    return $i18n->translatePlural($text, $alternative, $count);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 * @param mixed  ...$replacements
 *
 * @return string
 */
function _pf(string $text, string $alternative, int $count, ...$replacements): string
{
    global $i18n;

    return $i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
}

/**
 * @param string $text
 * @param string $alternative
 * @param int    $count
 * @param mixed  ...$replacements
 *
 * @return string
 */
function _pfe(string $text, string $alternative, int $count, ...$replacements): string
{
    global $i18n;

    return $i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
}

/**
 * @param string $text
 * @param string $context
 *
 * @return string
 */
function _c(string $text, string $context): string
{
    global $i18n;

    return $i18n->translateWithContext($text, $context);
}

/**
 * @param string $text
 *
 * @return string
 */
function _m(string $text): string
{
    global $i18n;

    return $i18n->markForTranslation($text);
}

/**
 * @param int $bytes
 * @param int $precision
 *
 * @return string
 */
function human_readable_size(int $bytes, int $precision): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    for ($i = 0; $bytes > 1024; ++$i) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * @param ContainerInterface $container
 *
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function remove_cached_files(ContainerInterface $container): void
{
    $settings    = $container->get(Configuration::class)->all();
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? VIEWS_DIR . 'cache/';
    if (file_exists($cache)) {
        removeDirectory($cache, false);
    }
    removeDirectory($settings['di_compilation_path'], false);
    removeDirectory(dirname($settings['router_cache_file']), false);
}

/**
 * @param null|string $path
 * @param bool        $removePath
 *
 * @return bool
 */
function removeDirectory(?string $path, bool $removePath): bool
{
    if (empty($path)) {
        return true;
    }
    $iterator = new DirectoryIterator($path);
    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isDot() || !$fileInfo->isDir()) {
            continue;
        }
        $dirName = $fileInfo->getPathname();
        removeDirectory($dirName, true);
    }

    $files = new DirectoryIterator($path);
    $types = ['php', 'cache'];
    foreach ($files as $file) {
        if (in_array($file->getExtension(), $types)) {
            unlink($file->getPathname());
        }
    }

    if ($removePath) {
        return rmdir($path);
    }

    return true;
}
