<?php

declare(strict_types=1);

namespace App\View;

use Slim\Flash\Messages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigMessagesExtension.
 */
class TwigMessagesExtension extends AbstractExtension
{
    protected Messages $flash;

    /**
     * TwigMessagesExtension constructor.
     *
     * @param Messages $flash
     */
    public function __construct(Messages $flash)
    {
        $this->flash = $flash;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'slim-twig-flash';
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [
                $this,
                'getMessages',
            ]),
            new TwigFunction('has_message', [
                $this,
                'hasMessage',
            ]),
            new TwigFunction('form_data', [
                $this,
                'formData',
            ]),
            new TwigFunction('errors', [
                $this,
                'errors',
            ]),
        ];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasMessage(string $key): bool
    {
        return $this->flash->hasMessage($key);
    }

    /**
     * @param null|string $key
     *
     * @return array|mixed
     */
    public function getMessages(?string $key = null): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            file_put_contents(LOGS_DIR . 'flash.log', 'flash not active' . PHP_EOL, FILE_APPEND);

            return [];
        }
        if ($key !== null) {
            return $this->flash->getMessage($key);
        }

        return $this->flash->getMessages();
    }

    /**
     * Get the form data.
     *
     * @param string $key
     *
     * @return string
     */
    public function formData(string $key): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            file_put_contents(LOGS_DIR . 'flash.log', 'flash not active' . PHP_EOL, FILE_APPEND);

            return '';
        }

        $old = $this->flash->getFirstMessage('old');

        return $old[$key] ?? '';
    }

    /**
     * Get the errors.
     *
     * @param string $key
     *
     * @return array
     */
    public function errors(string $key): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            file_put_contents(LOGS_DIR . 'flash.log', 'flash not active' . PHP_EOL, FILE_APPEND);

            return [];
        }

        $errors = $this->flash->getFirstMessage('errors');

        return $errors[$key] ?? [];
    }
}
