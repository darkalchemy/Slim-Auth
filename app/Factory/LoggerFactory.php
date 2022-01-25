<?php

declare(strict_types=1);

namespace App\Factory;

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
     * The constructor.
     *
     * @param array $settings The settings
     */
    public function __construct(array $settings)
    {
        $this->path = (string) $settings['path'];
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
    public function addFileHandler(string $filename, int $level): self
    {
        if (!is_writeable($this->path)) {
            exit(_fe(
                '{0} is not writable by the webserver.<br>Please run:<br>sudo chown -R www-data:www-data {0}<br>sudo chmod -R 0775 {0}',
                $this->path
            ));
        }
        $filename            = sprintf('%s/%s', $this->path, $filename);
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0755);

        // The last "true" here tells monolog to remove empty []'s
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->handler[] = $rotatingFileHandler;

        return $this;
    }

    /**
     * Add a console logger.
     *
     * @return self The instance
     */
    public function addConsoleHandler(int $level): self
    {
        $streamHandler = new StreamHandler('php://stdout', $level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->handler[] = $streamHandler;

        return $this;
    }
}
