<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Interfaces\Service\EntityServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService implements EntityServiceInterface
{
    const ENTITY_NAME = User::class;

    /**
     * @var User|null
     */
    private $user = null;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

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
     * @param EventService                 $eventService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityService                $entityService
     * @param FlashBagService              $flashBagService
     *
     * @throws ReflectionException
     */
    public function __construct(
        EventService $eventService,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityService $entityService,
        FlashBagService $flashBagService
    ) {
        $this->eventService = $eventService;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityService = $entityService;
        $this->flashBagService = $flashBagService;

        $this->reflectionClass = $this->entityService->getNewReflectionClass(self::ENTITY_NAME);
    }

    /**
     * @param UserDto $userDto
     *
     * @return User
     */
    public function createUserFromDto(UserDto $userDto): User
    {
        $user = new User();

        /** @var User $user */
        $user = $this->entityService->populateEntityWithDto($user, $userDto);

        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

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
     * @throws ReflectionException
     */
    public function createUserDtoFromUser(User $user): UserDto
    {
        $userDto = new UserDto();

        /** @var UserDto $userDto */
        $userDto = $this->entityService->populateDtoWithEntity($user, $userDto);

        return $userDto;
    }

    /**
     * @param UserDto $userDto
     * @param User    $user
     *
     * @return User
     */
    public function updateUserFromDto(UserDto $userDto, User $user): User
    {
        if (null !== $userDto->password) {
            $userDto->password = $this->passwordEncoder->encodePassword($user, $userDto->password);
        }

        /** @var User $user */
        $user = $this->entityService->populateEntityWithDto($user, $userDto);

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
     * @return EventInterface
     */
    public function getEvent(): EventInterface
    {
        $events = $this->getEventService()->getEvents();

        return $events[$this->getEntityName()];
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
        return $this->reflectionClass->getShortName();
    }

    /**
     * @return ViewEventInterface
     */
    public function getViewEvent(): ViewEventInterface
    {
        $viewEvents = $this->getEventService()->getViewEvents();

        return $viewEvents[$this->getEntityName()];
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
