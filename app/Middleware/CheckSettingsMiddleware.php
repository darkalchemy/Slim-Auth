<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Factory\LoggerFactory;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;

/**
 * Class CheckMailMiddleware.
 */
class CheckSettingsMiddleware implements MiddlewareInterface
{
    protected array $settings;
    protected LoggerInterface       $logger;
    protected Messages $flash;

    /**
     * CheckMailMiddleware constructor.
     *
     * @param array         $settings
     * @param LoggerFactory $loggerFactory
     * @param Messages      $flash
     *
     * @throws Exception
     */
    public function __construct(array $settings, LoggerFactory $loggerFactory, Messages $flash)
    {
        $this->settings = $settings;
        $this->logger   = $loggerFactory->addFileHandler('settings.log')->createInstance('settings');
        $this->flash    = $flash;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->settings['mail']['smtp_enable']) {
            $this->flash->addMessage('error', _f('You must set up mail settings to send mail.'));
            $this->logger->error('You must set up mail settings to send mail.');
        }

        $paths = [
            $this->settings['site']['di_compilation_path'],
            $this->settings['router']['cache_file'],
            $this->settings['twig']['cache'],
        ];
        foreach ($paths as $path) {
            if (!empty($path) && !is_writeable($path)) {
                $this->flash->addMessage('error', _fe('{0} is not writable by the webserver.', $path));
                $this->logger->error(sprintf('%s is not writable by the webserver. Please run: sudo chown -R www-data:www-data %s;sudo chmod -R 0775 %s', $path, $path, $path));
            }
        }

        return $handler->handle($request);
    }
}
