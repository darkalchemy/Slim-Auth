<?php

declare(strict_types=1);

namespace App\Extensions;

use Delight\I18n\I18n;
use Delight\I18n\Throwable\LocaleNotSupportedException;
use Odan\Session\PhpSession;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigTranslationExtension.
 */
class TwigTranslationExtension extends AbstractExtension
{
    protected I18n       $i18n;
    protected PhpSession $phpSession;
    protected array $locales;

    /**
     * TwigTranslationExtension constructor.
     */
    public function __construct(I18n $i18n, PhpSession $phpSession)
    {
        $this->i18n       = $i18n;
        $this->phpSession = $phpSession;
        $this->locales    = $this->i18n->getSupportedLocales();

        $this->set_user_locale();
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('_f', [$this, 'translateFormatted']),
            new TwigFunction('_fe', [$this, 'translateFormattedExtended']),
            new TwigFunction('_p', [$this, 'translatePlural']),
            new TwigFunction('_pf', [$this, 'translatePluralFormatted']),
            new TwigFunction('_pfe', [$this, 'translatePluralFormattedExtended']),
            new TwigFunction('_c', [$this, 'translateWithContext']),
            new TwigFunction('locale', [$this, 'locale']),
            new TwigFunction('get_user_locale', [$this, 'get_user_locale']),
            new TwigFunction('native_language_name', [$this, 'native_language_name']),
            new TwigFunction('supported_locales', [$this, 'supported_locales']),
        ];
    }

    /**
     * @return string
     */
    public function translateFormatted(string $text)
    {
        return $this->i18n->translateFormatted($text);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translateFormattedExtended(string $text, ...$replacements)
    {
        return $this->i18n->translateFormattedExtended($text, ...$replacements);
    }

    /**
     * @return string
     */
    public function translatePlural(string $text, string $alternative, int $count)
    {
        return $this->i18n->translatePlural($text, $alternative, $count);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translatePluralFormatted(string $text, string $alternative, int $count, ...$replacements)
    {
        return $this->i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translatePluralFormattedExtended(string $text, string $alternative, int $count, ...$replacements)
    {
        return $this->i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
    }

    /**
     * @return string
     */
    public function translateWithContext(string $text, string $context)
    {
        return $this->i18n->translateWithContext($text, $context);
    }

    public function supported_locales()
    {
        return $this->locales;
    }

    /**
     * @return null|mixed|string
     */
    public function get_user_locale()
    {
        return $this->phpSession->get('lang') ?? $this->locales[0];
    }

    public function set_user_locale()
    {
        try {
            $this->i18n->setLocaleManually($this->get_user_locale());
        } catch (LocaleNotSupportedException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return null|string
     */
    public function native_language_name(string $locale)
    {
        return $this->i18n->getNativeLanguageName($locale);
    }
}
