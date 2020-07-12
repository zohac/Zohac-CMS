<?php

namespace App\Factory;

use App\Entity\User;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Factory\EntityFactoryInterface;
use App\Traits\EntityFactoryTrait;
use ReflectionException;

class UserFactory implements EntityFactoryInterface
{
    use EntityFactoryTrait;

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws ReflectionException
     * @throws UuidException
     */
    public function populateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $reflectionDto = $this->getNewReflectionClass($dto);
        $reflectionEntity = $this->getNewReflectionClass($entity);

        foreach ($reflectionDto->getProperties() as $property) {
            $propertyName = $property->getName();
            $setMethod = 'set'.ucfirst($propertyName);

            if ($reflectionEntity->hasMethod($setMethod)) {
                if ('uuid' === $propertyName && null === $dto->$propertyName) {
                    $dto->$propertyName = $this->getUuid();
                }
                if (null !== $dto->$propertyName) {
                    $entity->$setMethod($dto->$propertyName);
                }
            }
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof User;
    }
}
