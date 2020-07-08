<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashBagService
{
    const FLASH_SUCCESS = 'success';
    const FLASH_INFO = 'info';
    const FLASH_ERROR = 'error';

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorService
     */
    private $translatorService;

    public function __construct(FlashBagInterface $flashBag, TranslatorService $translatorService)
    {
        $this->flashBag = $flashBag;
        $this->translatorService = $translatorService;
    }

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     * @param string|null $domaine
     *
     * @return $this
     */
    public function addAndTransFlashMessage(
        ?string $title = null,
        ?string $content = null,
        ?string $domaine = null,
        string $type = self::FLASH_SUCCESS
    ): self {
        $title = $this->trans($title, $domaine);
        $content = $this->trans($content, $domaine);

        $this->addFlashMessage($title, $content, $type);

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
        return $this->translatorService->trans($string, $domain, $args, $locale);
    }

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     *
     * @return $this
     */
    public function addFlashMessage(
        ?string $title = null,
        ?string $content = null,
        string $type = self::FLASH_SUCCESS
    ): self {
        $message = [
            'title' => $title,
            'message' => $content,
        ];
        $this->flashBag->add($type, $message);

        return $this;
    }
}
