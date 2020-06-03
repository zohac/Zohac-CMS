<?php

namespace App\Service\User;

use App\Dto\user\UserDto;
use App\Entity\User;
use App\Event\User\UserPostCreateEvent;
use App\Event\User\UserPostUpdateEvent;
use App\Exception\UuidException;
use App\Service\DefaultService;
use App\Service\EventService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService extends DefaultService
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
     * UserService constructor.
     *
     * @param SerializerInterface          $serializer
     * @param EventDispatcherInterface     $eventDispatcher
     * @param ValidatorInterface           $validator
     * @param EventService                 $eventService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface       $entityManager
     * @param UuidService                  $uuidService
     */
    public function __construct(
        SerializerInterface $serializer,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        EventService $eventService,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        UuidService $uuidService
    ) {
        parent::__construct($serializer, $eventDispatcher, $validator, $eventService);

        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->uuidService = $uuidService;
    }

    /**
     * @param UserDto $userDto
     *
     * @return User
     *
     * @throws UuidException
     */
    public function createUserFromDto(UserDto $userDto): User
    {
        $user = new User();

        $user->setUuid($this->getUuid());
        $user->setEmail($userDto->email);
        $user->setRoles($userDto->roles);

        $password = $this->passwordEncoder->encodePassword($user, $userDto->password);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->dispatchEvent(UserPostCreateEvent::NAME, ['user' => $user]);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return UserDto
     */
    public function createUserDtoFromUser(User $user): UserDto
    {
        $userDto = new UserDto();

        $userDto->email = $user->getEmail();
        $userDto->roles = $user->getRoles();

        return $userDto;
    }

    /**
     * @param UserDto $userDto
     * @param User    $user
     *
     * @return User
     *
     * @throws UuidException
     */
    public function updateUserFromDto(UserDto $userDto, User $user): User
    {
        $user->setUuid($this->getUuid());
        $user->setEmail($userDto->email);
        $user->setRoles($userDto->roles);

        if (null !== $userDto->password) {
            $password = $this->passwordEncoder->encodePassword($user, $userDto->password);
            $user->setPassword($password);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->dispatchEvent(UserPostUpdateEvent::NAME, ['user' => $user]);

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
}
