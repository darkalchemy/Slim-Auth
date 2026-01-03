<?php

declare(strict_types=1);

namespace App\Factory;

use App\Utility\SetPermissions;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

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
     * @var SetPermissions
     */
    private SetPermissions $setPermissions;

    /**
     * @param array          $settings       The settings
     * @param SetPermissions $setPermissions
     */
    public function __construct(array $settings, SetPermissions $setPermissions)
    {
        $this->path           = (string) $settings['path'];
        $this->setPermissions = $setPermissions;
    }

    /**
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
     * @param string $filename The filename
     * @param int    $level    The level
     *
     * @return $this
     */
    public function addFileHandler(string $filename, int $level = Logger::DEBUG): self
    {
        if (!is_writable($this->path)) {
            $this->setPermissions->setPermission($this->path, 0777);
        }

        $filename = sprintf('%s/%s', $this->path, $filename);

        /** @phpstan-ignore-next-line */
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0755);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->handler[] = $rotatingFileHandler;

        return $this;
    }

    /**
     * @param int $level The level
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
