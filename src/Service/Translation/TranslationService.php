<?php

namespace App\Service\Translation;

use App\Dto\Translation\TranslationDto;
use App\Entity\Translation;
use App\Exception\UuidException;
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
     * @var TranslationHydratorService
     */
    private $hydrator;

    /**
     * TranslationService constructor.
     *
     * @param Translation                $translation
     * @param TranslationHydratorService $hydrator
     */
    public function __construct(Translation $translation, TranslationHydratorService $hydrator)
    {
        $this->translation = $translation;
        $this->hydrator = $hydrator;
    }

    /**
     * @param TranslationDto $translationDto
     *
     * @return Translation
     *
     * @throws UuidException
     */
    public function createTranslationFromDto(TranslationDto $translationDto): Translation
    {
        $translation = $this->getNewTranslation();
        /** @var Translation $translation */
        $translation = $this->hydrator->hydrateEntityWithDto($translation, $translationDto);

        return $translation;
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
