<?php

namespace App\Service;

use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
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
     * @var UuidService
     */
    private $uuidService;

    /**
     * EntityService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UuidService            $uuidService
     */
    public function __construct(EntityManagerInterface $entityManager, UuidService $uuidService)
    {
        $this->entityManager = $entityManager;
        $this->uuidService = $uuidService;
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws ReflectionException
     * @throws UuidException
     */
    public function populateEntityWithDto(EntityInterface $entity, DtoInterface $dto)
    {
        $reflectionDto = $this->getNewReflectionClass($dto);
        $reflectionEntity = $this->getNewReflectionClass($entity);

        foreach ($reflectionDto->getProperties() as $property) {
            $propertyName = $property->getName();
            $setMethod = 'set'.ucfirst($propertyName);

            $this->populate($reflectionEntity, $setMethod, $propertyName, $dto, $entity);
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
     * @param ReflectionClass $reflectionEntity
     * @param string          $setMethod
     * @param string          $propertyName
     * @param DtoInterface    $dto
     * @param EntityInterface $entity
     *
     * @throws UuidException
     */
    public function populate(ReflectionClass $reflectionEntity, string $setMethod, string $propertyName, DtoInterface $dto, EntityInterface $entity): void
    {
        if ($reflectionEntity->hasMethod($setMethod)) {
            if ('uuid' === $propertyName && null === $dto->$propertyName) {
                $dto->$propertyName = $this->getUuid();
            }
            if (null !== $dto->$propertyName) {
                $entity->$setMethod($dto->$propertyName);
            }
        }
    }

    /**
     * @return string
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        $uuid = $this->uuidService->create();

        if (!$uuid) {
            throw new UuidException('L\'application ne parviens pas à générer un uuid.');
        }

        return $uuid;
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
