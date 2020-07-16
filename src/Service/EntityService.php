<?php

namespace App\Service;

use App\Exception\DtoHandlerException;
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
     * @var DtoHandler
     */
    private $dtoHandler;

    /**
     * EntityService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventService           $eventService
     * @param HydratorService        $hydratorService
     * @param DtoHandler             $dtoHandler
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventService $eventService,
        HydratorService $hydratorService,
        DtoHandler $dtoHandler
    ) {
        $this->entityManager = $entityManager;
        $this->eventService = $eventService;
        $this->hydratorService = $hydratorService;
        $this->dtoHandler = $dtoHandler;
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws HydratorException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $entity = $this->hydratorService->hydrateEntityWithDto($entity, $dto);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return DtoInterface
     *
     * @throws HydratorException
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        return $this->hydratorService->hydrateDtoWithEntity($entity, $dto);
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
     * @return DtoInterface
     *
     * @throws DtoHandlerException
     */
    public function getDto(): DtoInterface
    {
        if (null === $this->dto || !$this->dto instanceof DtoInterface) {
            $this->dto = $this->dtoHandler->getDtoInterface($this->entity);
        }

        return $this->dto;
    }

    /**
     * @return DtoInterface
     *
     * @throws DtoHandlerException
     * @throws HydratorException
     */
    public function getAndHydrateDto(): DtoInterface
    {
        $this->getDto();

        $this->hydrateDtoWithEntity($this->entity, $this->dto);

        return $this->dto;
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
