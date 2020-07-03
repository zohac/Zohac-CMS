<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Exception\UuidException;
use App\Service\EventService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * UserService constructor.
     *
     * @param EventService                 $eventService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface       $entityManager
     * @param UuidService                  $uuidService
     */
    public function __construct(
        EventService $eventService,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        UuidService $uuidService
    ) {
        $this->eventService = $eventService;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->uuidService = $uuidService;
    }

    /**
     * @param UserDto $userDto
     *
     * @return User
     */
    public function createUserFromDto(UserDto $userDto): User
    {
        $user = new User();

        $user = $this->populateUserWithDto($user, $userDto);

        $this->eventService->dispatchEvent(UserEvent::POST_CREATE, ['user' => $user]);

        return $user;
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
     * @param User $user
     *
     * @return UserDto
     */
    public function createUserDtoFromUser(User $user): UserDto
    {
        $userDto = new UserDto();

        $userDto->uuid = $user->getUuid();
        $userDto->email = $user->getEmail();
        $userDto->roles = $user->getRoles();
        $userDto->locale = $user->getLocale();

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
        $user = $this->populateUserWithDto($user, $userDto);

        $this->eventService->dispatchEvent(UserEvent::POST_UPDATE, ['user' => $user]);

        return $user;
    }

    /**
     * @param User    $user
     * @param UserDto $userDto
     *
     * @return User
     *
     * @throws UuidException
     */
    public function populateUserWithDto(User $user, UserDto $userDto): User
    {
        $user->setUuid($this->getUuid());
        $user->setEmail($userDto->email);
        $user->setRoles($userDto->roles);
        $user->setLocale($userDto->locale);

        if (null !== $userDto->password) {
            $password = $this->passwordEncoder->encodePassword($user, $userDto->password);
            $user->setPassword($password);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function deleteUser(User $user): self
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->eventService->dispatchEvent(UserEvent::POST_DELETE);

        return $this;
    }
}
