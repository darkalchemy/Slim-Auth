<?php

declare(strict_types=1);

namespace App\Factory;

use DI\Container;
use DI\ContainerBuilder;
use Exception;

class ContainerFactory
{
    /**
     * @return Container
     * @throws Exception
     */
    public function createContainer(): Container
    {
        $file = CONFIG_DIR . 'settings.php';
        if (!file_exists($file)) {
            die(sprintf(
                '%s does not exist.<br>please run:<br>cp %s %s<br>and edit as needed.',
                $file,
                CONFIG_DIR . 'settings.example.php',
                $file
            ));
        }

        $builder  = new ContainerBuilder();
        $builder->addDefinitions(BOOTSTRAP_DIR . 'container.php');
        $settings = require $file;

        if ($settings['di_compilation_path']) {
            $builder->enableCompilation((string) $settings['di_compilation_path']);
            $builder->writeProxiesToFile(true, PROXIES_DIR);
        }

        return $builder->build();
    }
}
