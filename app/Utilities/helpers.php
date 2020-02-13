<?php

declare(strict_types = 1);

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
