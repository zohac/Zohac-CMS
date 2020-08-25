<?php

namespace App\Service\Translation;

use App\Dto\Translation\TranslationDto;
use App\Entity\Language;
use App\Entity\Translation;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Repository\LanguageRepository;
use App\Service\UuidService;

class TranslationHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * TranslationHydratorService constructor.
     *
     * @param UuidService        $uuidService
     * @param LanguageRepository $languageRepository
     */
    public function __construct(UuidService $uuidService, LanguageRepository $languageRepository)
    {
        $this->uuidService = $uuidService;
        $this->languageRepository = $languageRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /** @var Translation $entity */
        /** @var TranslationDto $dto */
        $uuid = (null !== $dto->uuid) ? $dto->uuid : $this->getUuid();
        $language = $this->languageRepository->findOneBy(['uuid' => $dto->language]);

        $entity->setUuid($uuid)
            ->setLanguage($language)
            ->setMessage($dto->message);

        return $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        /* @var Translation $entity */
        /* @var TranslationDto $dto */
        $dto->uuid = $entity->getUuid();
        $dto->language = $entity->getLanguage()->getUuid();
        $dto->message = $entity->getMessage();

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Language;
    }
}
