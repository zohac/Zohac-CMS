<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Exception\DtoHandlerException;
use App\Exception\HydratorException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionClass;
use ReflectionException;

class UserService implements EntityServiceInterface
{
    const ENTITY_NAME = User::class;

    /**
     * @var User|null
     */
    private $user = null;

    /**
     * @var UserDto|null
     */
    private $dto = null;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * UserService constructor.
     *
     * @param EventService    $eventService
     * @param EntityService   $entityService
     * @param FlashBagService $flashBagService
     *
     * @throws ReflectionException
     */
    public function __construct(
        EventService $eventService,
        EntityService $entityService,
        FlashBagService $flashBagService
    ) {
        $this->eventService = $eventService;
        $this->entityService = $entityService;
        $this->flashBagService = $flashBagService;

        $this->reflectionClass = $this->entityService->getNewReflectionClass(self::ENTITY_NAME);
    }

    /**
     * @param UserDto $userDto
     *
     * @return User
     *
     * @throws HydratorException
     */
    public function createUserFromDto(UserDto $userDto): User
    {
        /** @var User $user */
        $user = $this->entityService->hydrateEntityWithDto(new User(), $userDto);

        $this->eventService->dispatchEvent(UserEvent::POST_CREATE, ['user' => $user]);

        $this->flashBagService->addAndTransFlashMessage(
            'User',
            'User successfully created.',
            'user'
        );

        return $user;
    }

    /**
     * @param User $user
     *
     * @return UserDto
     *
     * @throws HydratorException
     * @throws DtoHandlerException
     */
    public function createUserDtoFromUser(User $user): UserDto
    {
        return $this->entityService->getAndHydrateDto($user);
    }

    /**
     * @param UserDto $userDto
     * @param User    $user
     *
     * @return User
     *
     * @throws HydratorException
     */
    public function updateUserFromDto(UserDto $userDto, User $user): User
    {
        /** @var User $user */
        $user = $this->entityService->hydrateEntityWithDto($user, $userDto);

        $this->eventService->dispatchEvent(UserEvent::POST_UPDATE, ['user' => $user]);

        $this->flashBagService->addAndTransFlashMessage(
            'User',
            'User successfully updated.',
            'user'
        );

        return $user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function deleteUser(User $user): self
    {
        $this->entityService
            ->remove($user)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'User',
            'User successfully deleted.',
            $this->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(UserEvent::POST_DELETE);

        return $this;
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
    public function getFormType(): string
    {
        return $this->formType;
    }

    /**
     * @param $formType
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
    public function getEntityNamePlural(): string
    {
        return strtolower($this->reflectionClass->getShortName().'s');
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
     * @param string $eventName
     *
     * @return string
     */
    public function getEvent(string $eventName): string
    {
        $events = $this->getEventService()->getEvents();

        return $events[self::ENTITY_NAME];
    }

    /**
     * @return EventService
     */
    public function getEventService(): EventService
    {
        return $this->eventService;
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
     * @param string $eventName
     *
     * @return string
     */
    public function getViewEvent(string $eventName): string
    {
        $viewEvents = $this->getEventService()->getViewEvents();

        return $viewEvents[self::ENTITY_NAME];
    }

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->user;
    }

    /**
     * @param EntityInterface $user
     *
     * @return $this
     */
    public function setEntity(EntityInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDeleteMessage(): string
    {
        return $this->flashBagService->trans(
            'Are you sure you want to delete this user (%email%) ?',
            'user',
            ['email' => $this->user->getEmail()]
        );
    }
}
