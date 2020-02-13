<?php

declare(strict_types = 1);

return static function (array $cookie) {
    date_default_timezone_set('UTC');

    ini_set('default_charset', 'utf-8');
    ini_set('max_execution_time', '300');
    ini_set('session.sid_length', (ini_get('session.save_handler') != 'files' ? '256' : '128'));
    ini_set('session.lazy_write', '0');
    ini_set('session.name', $cookie['name']);
    ini_set('session.sid_bits_per_character', '6');
    ini_set('session.hash_function', 'sha512');
    ini_set('session.entropy_length', '1024');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_trans_sid', '0');

    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_domain', '');
    ini_set('session.cookie_httponly', $cookie['http_only']);
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_samesite', $cookie['samesite']);
    ini_set('session.cookie_secure', $cookie['secure']);

    ini_set('session.gc_divisor', '1');
    ini_set('session.gc_maxlifetime', '1350');
};
