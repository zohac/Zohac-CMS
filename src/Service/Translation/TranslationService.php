<?php

namespace App\Service\Translation;

use App\Dto\Translation\TranslationDto;
use App\Entity\Translation;
use App\Exception\UuidException;

class TranslationService
{
    /**
     * @var Translation
     */
    private $translation;

    /**
     * @var TranslationHydratorService
     */
    private $hydrator;

    /**
     * TranslationService constructor.
     *
     * @param TranslationHydratorService $hydrator
     */
    public function __construct(TranslationHydratorService $hydrator)
    {
        $this->hydrator = $hydrator;
        $this->translation = new Translation();
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

    public function getNewTranslation(): Translation
    {
        return clone $this->translation;
    }
}
