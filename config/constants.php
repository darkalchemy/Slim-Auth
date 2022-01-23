<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
const CONFIG_DIR             = ROOT_DIR . 'config' . DIRECTORY_SEPARATOR;
const BOOTSTRAP_DIR          = ROOT_DIR . 'bootstrap' . DIRECTORY_SEPARATOR;
const ROUTES_DIR             = ROOT_DIR . 'routes' . DIRECTORY_SEPARATOR;
const VAR_DIR                = ROOT_DIR . 'var' . DIRECTORY_SEPARATOR;
const CACHE_DIR              = VAR_DIR . 'cache' . DIRECTORY_SEPARATOR;
const LOGS_DIR               = VAR_DIR . 'logs' . DIRECTORY_SEPARATOR;
const PROXIES_DIR            = VAR_DIR . 'proxies' . DIRECTORY_SEPARATOR;
const RESOURCES_DIR          = ROOT_DIR . 'resources' . DIRECTORY_SEPARATOR;
const DATABASE_DIR           = ROOT_DIR . 'database' . DIRECTORY_SEPARATOR;
const VIEWS_DIR              = RESOURCES_DIR . 'views' . DIRECTORY_SEPARATOR;
const PUBLIC_DIR             = ROOT_DIR . 'public' . DIRECTORY_SEPARATOR;
const VENDOR_DIR             = ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR;
const PUBLIC_RESOURCES_DIR   = PUBLIC_DIR . 'resources' . DIRECTORY_SEPARATOR;
const LOCALE_DIR             = ROOT_DIR . 'locale' . DIRECTORY_SEPARATOR;

const ENV                    = 'DEVELOPMENT'; // DEVELOPMENT or PRODUCTION
const SITE_NAME              = 'Slim-Auth';
const SESSION_HANDLER        = 'files'; // files, redis, memcached
