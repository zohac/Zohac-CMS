<?php

namespace App\Service\Translation;

use App\Dto\Translation\TranslationDto;
use App\Entity\Translation;
use App\Exception\UuidException;
use App\Repository\LanguageRepository;
use App\Service\UuidService;

class TranslationService
{
    /**
     * @var Translation
     */
    private $translation;
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    public function __construct(
        Translation $translation,
        UuidService $uuidService,
        LanguageRepository $languageRepository
    ) {
        $this->translation = $translation;
        $this->uuidService = $uuidService;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param TranslationDto $translationDto
     * @return Translation
     * @throws UuidException
     */
    public function createTranslationFromDto(TranslationDto $translationDto): Translation
    {
        $language = $this->languageRepository->findOneBy(['uuid' => $translationDto->language]);

        return $this->getNewTranslation()
            ->setUuid($this->getUuid())
            ->setLanguage($language)
            ->setMessage($translationDto->message);
    }

    /**
     * @return string
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }

    public function getNewTranslation(): Translation
    {
        return clone $this->translation;
    }
}
