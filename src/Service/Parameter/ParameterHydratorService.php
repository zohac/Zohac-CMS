<?php

namespace App\Service\Parameter;

use App\Dto\Parameter\ParameterDto;
use App\Entity\Parameter;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;

class ParameterHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * ParameterHydratorService constructor.
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
    * @var Parameter    $entity
    * @var ParameterDto $dto
    */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $uuid = (null !== $dto->uuid) ? $dto->uuid : $this->getUuid();

        $entity->setUuid($uuid)
            ->setName($dto->name)
            ->setValue($dto->value)
        ;

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
    * @var Parameter
    * @var ParameterDto $dto
    */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $dto->name = $entity->getName();
        $dto->value = $entity->getValue();

        return $dto;
    }

    /**
    * {@inheritdoc}
    */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Parameter;
    }
}
