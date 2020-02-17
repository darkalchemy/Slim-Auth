<?php

declare(strict_types=1);

namespace App\Extensions;

use Delight\I18n\I18n;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigTranslationExtension extends AbstractExtension
{
    protected I18n $_i18n;

    /**
     * TwigTranslationExtension constructor.
     */
    public function __construct(I18n $i18n)
    {
        $this->_i18n = $i18n;
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
        ];
    }

    /**
     * @return string
     */
    public function translateFormatted(string $text)
    {
        return $this->_i18n->translateFormatted($text);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translateFormattedExtended(string $text, ...$replacements)
    {
        return $this->_i18n->translateFormattedExtended($text, ...$replacements);
    }

    /**
     * @return string
     */
    public function translatePlural(string $text, string $alternative, int $count)
    {
        return $this->_i18n->translatePlural($text, $alternative, $count);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translatePluralFormatted(string $text, string $alternative, int $count, ...$replacements)
    {
        return $this->_i18n->translatePluralFormatted($text, $alternative, $count, ...$replacements);
    }

    /**
     * @param mixed ...$replacements
     *
     * @return string
     */
    public function translatePluralFormattedExtended(string $text, string $alternative, int $count, ...$replacements)
    {
        return $this->_i18n->translatePluralFormattedExtended($text, $alternative, $count, ...$replacements);
    }

    /**
     * @return string
     */
    public function translateWithContext(string $text, string $context)
    {
        return $this->_i18n->translateWithContext($text, $context);
    }
}
