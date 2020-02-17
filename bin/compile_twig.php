<?php

declare(strict_types=1);

$container = (require_once __DIR__ . '/../bootstrap/app.php')->getContainer();
compile_twig_templates($container);
