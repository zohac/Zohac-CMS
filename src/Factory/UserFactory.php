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
                $this
                    ->uuidProperty($propertyName, $dto)
                    ->notNullProperty($propertyName, $dto, $setMethod, $entity);
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

    /**
     * @param string       $propertyName
     * @param DtoInterface $dto
     *
     * @return $this
     *
     * @throws UuidException
     */
    private function uuidProperty(string $propertyName, DtoInterface $dto): self
    {
        if ('uuid' === $propertyName && null === $dto->$propertyName) {
            $dto->$propertyName = $this->getUuid();
        }

        return $this;
    }

    /**
     * @param string          $propertyName
     * @param DtoInterface    $dto
     * @param string          $setMethod
     * @param EntityInterface $entity
     *
     * @return $this
     */
    private function notNullProperty(
        string $propertyName,
        DtoInterface $dto,
        string $setMethod,
        EntityInterface $entity
    ): self {
        if (null !== $dto->$propertyName) {
            $entity->$setMethod($dto->$propertyName);
        }

        return $this;
    }
}
