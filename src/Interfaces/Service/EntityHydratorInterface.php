<?php

namespace App\Interfaces\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use ReflectionClass;

interface EntityHydratorInterface
{
    /**
     * @return string
     */
    public function getUuid(): string;

    /**
     * @param object|string $object
     *
     * @return ReflectionClass
     */
    public function getNewReflectionClass($object): ReflectionClass;

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
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool;
}
