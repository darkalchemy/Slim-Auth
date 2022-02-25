<?php

declare(strict_types=1);

namespace App\View;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigMessagesExtension.
 */
class TwigMessagesExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container The container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            // @phpstan-ignore-next-line
            new TwigFunction('flash', [$this->container->get(TwigMessagesRuntime::class), 'getMessages']),
            // @phpstan-ignore-next-line
            new TwigFunction('has_message', [$this->container->get(TwigMessagesRuntime::class), 'hasMessage']),
            // @phpstan-ignore-next-line
            new TwigFunction('form_data', [$this->container->get(TwigMessagesRuntime::class), 'formData']),
            // @phpstan-ignore-next-line
            new TwigFunction('errors', [$this->container->get(TwigMessagesRuntime::class), 'errors']),
        ];
    }
}
