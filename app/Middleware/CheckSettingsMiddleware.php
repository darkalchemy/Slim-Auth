<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Flash\Messages;

/**
 * Class CheckMailMiddleware.
 */
class CheckSettingsMiddleware implements MiddlewareInterface
{
    protected array $settings;
    protected Messages $flash;

    /**
     * CheckMailMiddleware constructor.
     *
     * @param array    $settings
     * @param Messages $flash
     */
    public function __construct(array $settings, Messages $flash)
    {
        $this->settings = $settings;
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
        $response = $handler->handle($request);
        if (!$this->settings['mail']['smtp_enable']) {
            $this->flash->addMessage('error', _f('You must set up mail settings to send mail.'));
        }

        $paths = [
            $this->settings['site']['di_compilation_path'],
            $this->settings['router']['cache_file'],
            $this->settings['twig']['path'],
            $this->settings['twig']['cache'],
        ];
        foreach ($paths as $path) {
            if (!empty($path) && !is_writeable($path)) {
                $this->flash->addMessage('error', _fe('{0} is not writable by the webserver.<br>Please run:<br>sudo chown -R www-data:www-data {0}<br>sudo chmod -R 0775 {0}', $path));
            }
        }

        return $response;
    }
}
