<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\Role;
use App\Entity\User;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserHydratorService constructor.
     *
     * @param UuidService                  $uuidService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UuidService $uuidService, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->uuidService = $uuidService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface $entity
     * @param DtoInterface    $dto
     *
     * @return EntityInterface
     *
     * @throws UuidException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /** @var User $entity */
        /** @var UserDto $dto */
        $uuid = (null !== $dto->uuid) ? $dto->uuid : $this->getUuid();

        $entity->setUuid($uuid)
            ->setEmail($dto->email)
            ->setLocale($dto->locale)
            ->setToken($dto->tokenValidity)
            ->setTokenValidity($dto->tokenValidity);

        /** @var Role $role */
        foreach ($dto->roles as $role) {
            $entity->addRole($role);
        }

        if (null !== $dto->password) {
            $password = $this->passwordEncoder->encodePassword($entity, $dto->password);
            $entity->setPassword($password);
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        /* @var User $entity */
        /* @var UserDto $dto */
        $dto->uuid = $entity->getUuid();
        $dto->email = $entity->getEmail();
        $dto->roles = $entity->getRolesEntities();
        $dto->locale = $entity->getLocale();
        $dto->token = $entity->getToken();
        $dto->tokenValidity = $entity->getTokenValidity();

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof User;
    }
}
