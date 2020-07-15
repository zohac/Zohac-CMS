<?php

namespace App\Service;

use App\Exception\HydratorException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Interfaces\Service\EntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;

class EntityService implements EntityServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var DtoInterface
     */
    private $dto;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;
    /**
     * @var HydratorService
     */
    private $hydratorService;

    /**
     * EntityService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventService           $eventService
     * @param HydratorService        $hydratorService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventService $eventService,
        HydratorService $hydratorService
    ) {
        $this->entityManager = $entityManager;
        $this->eventService = $eventService;
        $this->hydratorService = $hydratorService;
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws HydratorException
     */
    public function createEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $entity = $this->hydratorService->hydrateEntityWithDto($entity, $dto);

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

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    /**
     * @param EntityInterface|string $entity
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function setEntity($entity): self
    {
        $this->entity = $entity;

        $this->reflectionClass = $this->getNewReflectionClass($entity);

        return $this;
    }

    /**
     * @return DtoInterface
     */
    public function getDto(): DtoInterface
    {
        return $this->dto;
    }

    /**
     * @param DtoInterface $dto
     *
     * @return $this
     */
    public function setDto(DtoInterface $dto): self
    {
        $this->dto = $dto;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return $this->formType;
    }

    /**
     * @param string $formType
     *
     * @return $this
     */
    public function setFormType(string $formType): self
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->reflectionClass->getName();
    }

    /**
     * @return string
     */
    public function getEntityShortName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    /**
     * @return string
     */
    public function getEntityNameToLower(): string
    {
        return strtolower($this->reflectionClass->getShortName());
    }

    /**
     * @return string
     */
    public function getEntityNamePlural(): string
    {
        return strtolower($this->reflectionClass->getShortName().'s');
    }

    /**
     * @return EventService
     */
    public function getEventService(): EventService
    {
        return $this->eventService;
    }

    /**
     * @param string $eventName
     *
     * @return string
     *
     * @throws ReflectionException
     */
    public function getEvent(string $eventName): string
    {
        $events = $this->getEventService()->getEvents();

        $reflection = $this->getNewReflectionClass($events[$this->reflectionClass->getName()]);

        return $reflection->getConstant($eventName);
    }

    /**
     * @param string $eventName
     *
     * @return ViewEventInterface
     *
     * @throws ReflectionException
     */
    public function getViewEvent(string $eventName): string
    {
        $viewEvents = $this->getEventService()->getViewEvents();

        $reflection = $this->getNewReflectionClass($viewEvents[$this->reflectionClass->getName()]);

        return $reflection->getConstant($eventName);
    }

    /**
     * @return string
     */
    public function getDeleteMessage(): string
    {
        return 'delete Message';
    }
}
