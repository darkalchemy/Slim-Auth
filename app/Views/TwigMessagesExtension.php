<?php

declare(strict_types = 1);

namespace App\Views;

use Slim\Flash\Messages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigMessagesExtension
 *
 * @package App\Views
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
    public function getName()
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
     * @param $key
     *
     * @return bool
     */
    public function hasMessage($key)
    {
        return (bool) $this->flash->hasMessage($key);
    }

    /**
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getMessages(?string $key = null)
    {
        if ($key !== null) {
            return $this->flash->getMessage($key);
        }

        return $this->flash->getMessages();
    }
}
