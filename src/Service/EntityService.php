<?php

namespace App\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Factory\EntityFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;

class EntityService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityFactoryInterface[]
     */
    private $factories;

    /**
     * EntityService constructor.
     *
     * @param iterable               $handlers
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(iterable $handlers, EntityManagerInterface $entityManager)
    {
        foreach ($handlers as $handler) {
            $this->factories[] = $handler;
        }

        $this->entityManager = $entityManager;
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     */
    public function populateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->canHandle($entity)) {
                $entity = $factory->populateEntityWithDto($entity, $dto);
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param object|string $object
     *
     * @return ReflectionClass
     *
     * @throws ReflectionException
     */
    public function getNewReflectionClass($object): ReflectionClass
    {
        return new ReflectionClass($object);
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return DtoInterface
     *
     * @throws ReflectionException
     */
    public function populateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $reflectionDto = $this->getNewReflectionClass($dto);
        $reflectionEntity = $this->getNewReflectionClass($entity);

        foreach ($reflectionDto->getProperties() as $property) {
            $propertyName = $property->getName();
            $getMethod = 'get'.ucfirst($propertyName);

            if ($reflectionEntity->hasMethod($getMethod)) {
                $dto->$propertyName = $entity->$getMethod();
            }
        }

        return $dto;
    }

    /**
     * @param object $entity
     *
     * @return $this
     */
    public function persist(object $entity): self
    {
        $this->entityManager->persist($entity);

        return $this;
    }

    /**
     * @param object $entity
     *
     * @return $this
     */
    public function remove(object $entity): self
    {
        $this->entityManager->remove($entity);

        return $this;
    }

    /**
     * @return $this
     */
    public function flush(): self
    {
        $this->entityManager->flush();

        return $this;
    }
}
