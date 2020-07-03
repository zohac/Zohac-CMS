<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorService
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $locale;

    public function __construct(TranslatorInterface $translator, string $defaultLocale = 'en')
    {
        $this->translator = $translator;
        $this->locale = $defaultLocale;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $defaultLocale
     *
     * @return $this
     */
    public function setLocale(string $defaultLocale): self
    {
        $this->locale = $defaultLocale;

        return $this;
    }

    /**
     * @param string      $string
     * @param string|null $domain
     * @param array       $args
     * @param string|null $locale
     *
     * @return string
     */
    public function trans(string $string, string $domain = null, array $args = [], ?string $locale = null): string
    {
        if (null === $locale) {
            $locale = $this->locale;
        }

        return $this->translator->trans($string, $args, $domain, $locale);
    }
}
