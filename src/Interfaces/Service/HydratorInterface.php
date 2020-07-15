<?php

namespace App\Interfaces\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;

interface HydratorInterface
{
    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface;

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return DtoInterface
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface;

    /**
     * @param EntityInterface $entity
     *
     * @return EntityHydratorInterface
     */
    public function handleHydrator(EntityInterface $entity): EntityHydratorInterface;
}
