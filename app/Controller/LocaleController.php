<?php

declare(strict_types=1);

namespace App\Controller;

use Delight\I18n\I18n;
use Delight\I18n\Throwable\LocaleNotSupportedException;
use Odan\Session\PhpSession;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class LocaleController.
 */
class LocaleController
{
    protected I18n                 $i18n;
    protected Messages             $flash;
    protected RouteParserInterface $routeParser;
    protected PhpSession           $session;
    protected array                $locales;
    protected string $current_url;

    /**
     * LocaleController constructor.
     *
     * @param I18n                 $i18n
     * @param Messages             $flash
     * @param RouteParserInterface $routeParser
     * @param PhpSession           $session
     */
    public function __construct(I18n $i18n, Messages $flash, RouteParserInterface $routeParser, PhpSession $session)
    {
        $this->i18n        = $i18n;
        $this->flash       = $flash;
        $this->routeParser = $routeParser;
        $this->session     = $session;
        $this->locales     = $this->i18n->getSupportedLocales();
        $this->current_url = $this->session->get('current_url') ?? 'home';
    }

    /**
     * @param ResponseInterface $response
     * @param string            $lang
     *
     * @throws LocaleNotSupportedException
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response, string $lang): ResponseInterface
    {
        if (in_array($lang, $this->locales)) {
            $this->session->set('lang', $lang);
            $this->session->set('locale', substr($lang, 0, 2));
            $this->i18n->setLocaleManually($lang);
            $this->flash->addMessage(
                'success',
                _fe(
                    'Current locale changed to: {0}.',
                    ucfirst($this->i18n->getNativeLanguageName($lang))
                )
            );
        }

        return $response->withHeader('Location', $this->routeParser->urlFor($this->current_url));
    }
}
