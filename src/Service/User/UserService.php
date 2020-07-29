<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class UserService implements ServiceInterface
{
    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var EventService
     */
    private $eventService;

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
     */
    public function __construct(
        EventService $eventService,
        EntityService $entityService,
        FlashBagService $flashBagService
    ) {
        $this->eventService = $eventService;
        $this->entityService = $entityService;
        $this->flashBagService = $flashBagService;
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
     *
     * @throws ReflectionException
     */
    public function deleteUser(User $user): self
    {
        $this->entityService
            ->setEntity($user)
            ->remove($user)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'User',
            'User successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(UserEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteSoftUser(User $user): self
    {
        $user->setArchived(true);

        $this->entityService
            ->setEntity($user)
            ->persist($user)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'User',
            'User successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(UserEvent::POST_DELETE);

        return $this;
    }

    public function getDeleteMessage(EntityInterface $entity): string
    {
        /* @var User $user */
        return $this->flashBagService->trans(
            'Are you sure you want to delete this user (%email%) ?',
            'user',
            ['email' => $entity->getEmail()]
        );
    }
}
