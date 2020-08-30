<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\Language;
use App\Entity\Role;
use App\Entity\User;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Repository\LanguageRepository;
use App\Repository\RoleRepository;
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
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * UserHydratorService constructor.
     *
     * @param UuidService                  $uuidService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LanguageRepository           $languageRepository
     * @param RoleRepository               $roleRepository
     */
    public function __construct(
        UuidService $uuidService,
        UserPasswordEncoderInterface $passwordEncoder,
        LanguageRepository $languageRepository,
        RoleRepository $roleRepository
    ) {
        $this->uuidService = $uuidService;
        $this->passwordEncoder = $passwordEncoder;
        $this->languageRepository = $languageRepository;
        $this->roleRepository = $roleRepository;
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
        /* @var User $entity */
        /** @var UserDto $dto */
        if ($language = $this->getLanguage($dto->language)) {
            $entity->setLanguage($language);
        }

        $entity->setUuid($this->getUuid($dto->uuid))
            ->setEmail($dto->email)
            ->setToken($dto->tokenValidity)
            ->setTokenValidity($dto->tokenValidity);

        $roles = $entity->getRolesEntities();
        foreach ($roles as $role) {
            $entity->removeRole($role);
        }

        $roles = [];
        if ($role = $this->roleRepository->findOneBy(['name' => 'ROLE_USER'])) {
            $roles[] = $role;
        }
        foreach ($dto->roles as $role) {
            $roles[] = $this->roleRepository->findOneBy(['uuid' => $role]);
        }

        foreach ($roles as $role) {
            $entity->addRole($role);
        }

        if (null !== $dto->password) {
            $password = $this->passwordEncoder->encodePassword($entity, $dto->password);
            $entity->setPassword($password);
        }

        return $entity;
    }

    /**
     * @param string $uuid
     *
     * @return Language
     */
    public function getLanguage(string $uuid): Language
    {
        return $this->languageRepository->findOneBy(['uuid' => $uuid]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function getUuid(?string $uuid = null): string
    {
        return (null !== $uuid) ? $uuid : $this->uuidService->create();
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
        $dto->language = $entity->getLanguage()->getUuid();
        $dto->token = $entity->getToken();
        $dto->tokenValidity = $entity->getTokenValidity();

        /** @var Role $role */
        foreach ($entity->getRolesEntities() as $role) {
            $dto->roles[] = $role->getUuid();
        }

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
