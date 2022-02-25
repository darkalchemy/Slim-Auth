<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Factory\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Umpirsky\PermissionsHandler\ChmodPermissionsSetter;

/**
 * Class CheckMailMiddleware.
 */
class CheckSettingsMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    private array $settings;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var Messages
     */
    private Messages $flash;

    /**
     * @var ChmodPermissionsSetter
     */
    private ChmodPermissionsSetter $permissionsSetter;

    /**
     * @param array         $settings      The settings
     * @param LoggerFactory $loggerFactory The loggerFactory
     * @param Messages      $flash         The flash
     */
    public function __construct(
        array $settings,
        LoggerFactory $loggerFactory,
        Messages $flash,
        ChmodPermissionsSetter $permissionsSetter
    ) {
        $this->settings          = $settings;
        $this->logger            = $loggerFactory->addFileHandler('settings.log')->createInstance('settings');
        $this->flash             = $flash;
        $this->permissionsSetter = $permissionsSetter;
    }

    /**
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->settings['mail']['smtp_enable']) {
            $this->flash->addMessage('error', __f('You must set up mail settings to send mail.'));
            $this->logger->error('You must set up mail settings to send mail.');
        }

        $paths = [
            $this->settings['di_compilation_path'],
            $this->settings['router_cache_file'],
            $this->settings['twig']['cache'],
        ];
        foreach ($paths as $path) {
            if (!empty($path) && file_exists($path) && !is_writable($path)) {
                $this->permissionsSetter->setPermissions($path);
            }
        }

        return $handler->handle($request);
    }
}
