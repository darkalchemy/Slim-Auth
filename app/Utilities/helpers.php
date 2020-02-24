<?php

declare(strict_types=1);

use Darkalchemy\Twig\TwigCompiler;
use Psr\Container\ContainerInterface;
use Selective\Config\Configuration;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

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
    $scheme = 'http';
    if (isset($_SERVER['REQUEST_SCHEME'])) {
        $scheme = $_SERVER['REQUEST_SCHEME'];
    } elseif (isset($_SERVER['HTTPS'])) {
        $scheme = 'https';
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        $url = parse_url($_SERVER['REQUEST_URI']);

        $scheme = $url[0];
    }

    return $scheme;
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
    $twig->addExtension(new TwigExtension());
    $compiler = new TwigCompiler($twig, $cache, true);

    try {
        $compiler->compile();
    } catch (Exception $e) {
        die($e->getMessage());
    }

    echo "\nCompiling twig templates completed\n\n";
    echo "to fix the permissions, you should run:\nsudo chown -R www-data:www-data {$cache}\nsudo chmod -R 0775 {$cache}\n";

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
