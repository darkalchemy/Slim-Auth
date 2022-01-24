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
        if ($key !== null) {
            return $this->flash->getMessage($key);
        }

        return $this->flash->getMessages();
    }
}
