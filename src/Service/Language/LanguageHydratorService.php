<?php

namespace App\Service\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;

class LanguageHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * LanguageHydratorService constructor.
     *
     * @param UuidService $uuidService
     */
    public function __construct(UuidService $uuidService)
    {
        $this->uuidService = $uuidService;
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws UuidException
     *
     * @var Language    $entity
     * @var LanguageDto $dto
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $uuid = (null !== $dto->uuid) ? $dto->uuid : $this->getUuid();

        $entity->setUuid($uuid)
            ->setName($dto->name)
            ->setAlternateName($dto->alternateName)
            ->setDescription($dto->description)
            ->setIso6391($dto->iso6391)
            ->setIso6392B($dto->iso6392B)
            ->setIso6392T($dto->iso6392T)
            ->setIso6393($dto->iso6393);

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
     *
     * @var Language
     * @var LanguageDto $dto
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $dto->uuid = $entity->getUuid();
        $dto->name = $entity->getName();
        $dto->alternateName = $entity->getAlternateName();
        $dto->description = $entity->getDescription();
        $dto->iso6391 = $entity->getIso6391();
        $dto->iso6392B = $entity->getIso6392B();
        $dto->iso6392T = $entity->getIso6392T();
        $dto->iso6393 = $entity->getIso6393();

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
