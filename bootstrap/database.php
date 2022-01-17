<?php

declare(strict_types=1);

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Native\SentinelBootstrapper;
use Illuminate\Database\Capsule\Manager as Capsule;

return static function ($settings) {
    Sentinel::instance(new SentinelBootstrapper((require CONFIG_DIR . 'sentinel.php')));
    $capsule = new Capsule();
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $capsule->bootEloquent();
};
