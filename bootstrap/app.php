<?php

declare(strict_types=1);

use App\Factory\ContainerFactory;
use Delight\I18n\I18n;
use Slim\App;

$startTime = hrtime(true);

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/constants.php';

$container = (new ContainerFactory())->createContainer();
$app       = $container->get(App::class);
$i18n      = $container->get(I18n::class);

return $app;
