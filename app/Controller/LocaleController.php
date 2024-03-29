<?php

declare(strict_types=1);

namespace App\Controller;

use Delight\I18n\I18n;
use Delight\I18n\Throwable\LocaleNotSupportedException;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class LocaleController.
 */
class LocaleController
{
    /**
     * @var I18n
     */
    protected I18n $i18n;

    /**
     * @var Messages
     */
    protected Messages $flash;

    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * @var array
     */
    protected array $locales;

    /**
     * @var string
     */
    protected string $current_url;

    /**
     * @param I18n                 $i18n        The i18n
     * @param Messages             $flash       The flash
     * @param RouteParserInterface $routeParser The routeParser
     * @param SessionInterface     $session     The sessionInterface
     */
    public function __construct(
        I18n $i18n,
        Messages $flash,
        RouteParserInterface $routeParser,
        SessionInterface $session
    ) {
        $this->i18n        = $i18n;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->session     = $session;
        $this->locales     = $this->i18n->getSupportedLocales();
        $this->current_url = $this->session->get('current_url') ?? 'home';
    }

    /**
     * @param ResponseInterface $response The response
     * @param string            $lang     The lang
     *
     * @throws LocaleNotSupportedException
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response, string $lang): ResponseInterface
    {
        if (in_array($lang, $this->locales)) {
            $this->session->set('locale', $lang);
            $this->i18n->setLocaleManually($lang);
            $this->flash->addMessage(
                'success',
                __fe(
                    'Current locale changed to: {0}.',
                    ucfirst((string) $this->i18n->getNativeLanguageName($lang))
                )
            );
        }

        return $response->withHeader('Location', $this->routeParser->urlFor($this->current_url));
    }
}
