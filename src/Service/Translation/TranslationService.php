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
        $entity = $this->getNewTranslation();
        /** @var Translation $entity */
        $entity = $this->hydrator->hydrateEntityWithDto($entity, $translationDto);

        return $entity;
    }

    /**
     * @param array $values
     *
     * @return Translation
     *
     * @throws UuidException
     */
    public function createTranslationFromArray(array $values): Translation
    {
        $entity = $this->getNewTranslation();
        /** @var Translation $entity */
        $entity = $this->hydrator->hydrateEntityWithArray($entity, $values);

        return $entity;
    }

    /**
     * @return Translation
     */
    public function getNewTranslation(): Translation
    {
        return clone $this->translation;
    }
}
