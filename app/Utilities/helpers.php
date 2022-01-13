<?php

declare(strict_types=1);

use Darkalchemy\Twig\TwigCompiler;
use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Slim\Views\Twig;

/**
 * @param $array
 * @param $keys
 *
 * @return array
 */
function array_clean($array, $keys)
{
    return array_intersect_key($array, array_flip($keys));
}

/**
 * @return mixed|string
 */
function get_scheme()
{
    if (isset($_SERVER['REQUEST_SCHEME'])) {
        return $_SERVER['REQUEST_SCHEME'];
    }
    if (isset($_SERVER['HTTPS'])) {
        return 'https';
    }
    if (isset($_SERVER['REQUEST_URI'])) {
        return ['scheme' => $scheme] = parse_url($_SERVER['REQUEST_URI']) + ['scheme' => 'http'];
    }
}

/**
 * @param ContainerInterface $container
 *
 * @return int
 */
function compile_twig_templates(ContainerInterface $container)
{
    $settings    = $container->get(Configuration::class)->all();
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? $settings['root'] . '/resources/views/cache/';
    $twig        = $container->get(Twig::class)->getEnvironment();
    $compiler    = new TwigCompiler($twig, $cache, true);

    try {
        $compiler->compile();
    } catch (Exception $e) {
        die($e->getMessage());
    }

    return 0;
}

/**
 * @param string $text
 * @param mixed  ...$replacements
 *
 * @return string
 */
function _f(string $text, ...$replacements)
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
function _fe(string $text, ...$replacements)
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
function _p(string $text, string $alternative, int $count)
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
function _pf(string $text, string $alternative, int $count, ...$replacements)
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
function _pfe(string $text, string $alternative, int $count, ...$replacements)
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
function _c(string $text, string $context)
{
    global $i18n;

    return $i18n->translateWithContext($text, $context);
}

/**
 * @param string $text
 *
 * @return string
 */
function _m(string $text)
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
function human_readable_size(int $bytes, int $precision)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    for ($i = 0; $bytes > 1024; ++$i) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

function remove_cached_files(ContainerInterface $container)
{
    $settings    = $container->get(Configuration::class)->all();
    $twig_config = $settings['twig'];
    $cache       = $twig_config['cache'] ?? $settings['root'] . '/resources/views/cache/';
    if (file_exists($cache)) {
        removeDirectory($cache, false);
    }
    removeDirectory($settings['site']['di_compilation_path'], false);
    removeDirectory(dirname($settings['router']['cache_file']), false);
}

function removeDirectory(?string $path, bool $contentsOnly): bool
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
        removeDirectory($dirName, $contentsOnly);
    }

    $files = new FilesystemIterator($path);
    $types = ['php', 'cache'];
    foreach ($files as $file) {
        if (in_array($file->getExtension(), $types)) {
            $fileName = $file->getPathname();

            try {
                unlink($fileName);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    if ($contentsOnly) {
        return rmdir($path);
    }

    return true;
}
