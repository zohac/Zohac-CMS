<?php

namespace App\Service;

use App\Interfaces\Dto\DtoInterface;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * EntityService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UuidService $uuidService
     */
    public function __construct(EntityManagerInterface $entityManager, UuidService $uuidService)
    {
        $this->entityManager = $entityManager;
        $this->uuidService = $uuidService;
    }

    /**
     * @param $entity
     * @param DtoInterface $dto
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function populateEntityWithDto($entity, DtoInterface $dto)
    {
        $reflectionDto = $this->getNewReflectionClass($dto);
        $reflectionEntity = $this->getNewReflectionClass($entity);

        foreach ($reflectionDto->getProperties() as $property) {
            $propertyName = $property->getName();
            $setMethod = 'set'.ucfirst($propertyName);

            if ($reflectionEntity->hasMethod($setMethod)) {
                $entity->$setMethod($dto->$propertyName);
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param object|string $object
     *
     * @return \ReflectionClass
     *
     * @throws \ReflectionException
     */
    public function getNewReflectionClass($object): \ReflectionClass
    {
        return new \ReflectionClass($object);
    }

    /**
     * @param $entity
     * @param DtoInterface $dto
     *
     * @return DtoInterface
     *
     * @throws \ReflectionException
     */
    public function populateDtoWithEntity($entity, DtoInterface $dto): DtoInterface
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

    /**
     * @return UuidService
     */
    public function getUuidService(): UuidService
    {
        return $this->uuidService;
    }
}
