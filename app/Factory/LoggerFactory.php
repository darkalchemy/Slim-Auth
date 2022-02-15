<?php

declare(strict_types=1);

namespace App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Umpirsky\PermissionsHandler\ChmodPermissionsSetter;

/**
 * Factory.
 */
class LoggerFactory
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @var array Handler
     */
    private array $handler = [];

    /**
     * @var ChmodPermissionsSetter
     */
    private ChmodPermissionsSetter $permissionsSetter;

    /**
     * The constructor.
     *
     * @param array $settings The settings
     */
    public function __construct(array $settings, ChmodPermissionsSetter $permissionsSetter)
    {
        $this->path              = (string) $settings['path'];
        $this->permissionsSetter = $permissionsSetter;
    }

    /**
     * Build the logger.
     *
     * @param string $name The name
     *
     * @return LoggerInterface The logger
     */
    public function createInstance(string $name): LoggerInterface
    {
        $logger = new Logger($name);

        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handler = [];

        return $logger;
    }

    /**
     * Add a console logger.
     *
     * @param string $filename
     * @param int    $level
     *
     * @return $this
     */
    public function addFileHandler(string $filename, int $level = Logger::DEBUG): self
    {
        if (!is_writeable($this->path)) {
            $this->permissionsSetter->setPermissions($this->path);
        }
        $filename            = sprintf('%s/%s', $this->path, $filename);

        /** @phpstan-ignore-next-line */
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0755);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->handler[] = $rotatingFileHandler;

        return $this;
    }

    /**
     * Add a console logger.
     *
     * @param int $level
     *
     * @return self The instance
     */
    public function addConsoleHandler(int $level = Logger::DEBUG): self
    {
        /** @phpstan-ignore-next-line */
        $streamHandler = new StreamHandler('php://stdout', $level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->handler[] = $streamHandler;

        return $this;
    }
}
