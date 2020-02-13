<?php

declare(strict_types = 1);

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Illuminate\Database\Capsule\Manager as Capsule;

return static function (Capsule $db, array $cookies) {
    Sentinel::instance(new SentinelBootstrapper((require __DIR__ . '/../config/sentinel.php')($cookies)));
};
