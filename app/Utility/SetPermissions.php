<?php

declare(strict_types=1);

namespace App\Utility;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * SetPermissions.
 */
class SetPermissions
{
    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function setPermission(string $path, int $octal)
    {
        try {
            if (is_dir($path)) {
                $this->filesystem->chmod($path, $octal, 0000, true);
            } else {
                $this->filesystem->chmod($path, $octal);
            }
        } catch (IOExceptionInterface $exception) {
            echo 'An error occurred while setting permissions on your directory at ' . $exception->getPath() . ': ' . $exception->getMessage();

            exit;
        }
    }
}
