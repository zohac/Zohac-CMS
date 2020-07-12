<?php

namespace App\Interfaces\Factory;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use ReflectionClass;

interface EntityFactoryInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool;

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     */
    public function populateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface;

    /**
     * @param object|string $object
     *
     * @return ReflectionClass
     */
    public function getNewReflectionClass($object): ReflectionClass;

    /**
     * @return string
     */
    public function getUuid(): string;
}
