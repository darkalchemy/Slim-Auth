<?php

declare(strict_types=1);

namespace App\Factory;

use DI\Container;
use DI\ContainerBuilder;
use Exception;

/**
 * ContainerFactory class.
 */
class ContainerFactory
{
    /**
     * @throws Exception
     *
     * @return Container
     */
    public function createContainer(): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(BOOTSTRAP_DIR . 'container.php');
        $settings = require CONFIG_DIR . 'settings.php';

        if ($settings['environment'] === 'PRODUCTION') {
            $builder->enableCompilation((string) $settings['di_compilation_path']);
            $builder->enableDefinitionCache($settings['site_name'] . '.');
        }

        return $builder->build();
    }
}
