<?php

namespace App\Service\Role;

use App\Dto\Role\RoleDto;
use App\Entity\Role;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Repository\RoleRepository;
use App\Service\Translatable\TranslatableService;
use App\Service\UuidService;
use Doctrine\ORM\NonUniqueResultException;

class RoleHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var TranslatableService
     */
    private $translatableService;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * RoleHydratorService constructor.
     * @param UuidService $uuidService
     * @param TranslatableService $translatableService
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        UuidService $uuidService,
        TranslatableService $translatableService,
        RoleRepository $roleRepository
    ) {
        $this->uuidService = $uuidService;
        $this->translatableService = $translatableService;
        $this->roleRepository = $roleRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface $entity
     * @param DtoInterface $dto
     * @return EntityInterface
     * @throws UuidException
     * @throws NonUniqueResultException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /* @var Role $entity */
        /* @var RoleDto $dto */
        $translatable = $this->translatableService->hydrateTranslatable($entity->getTranslatable(), $dto->translatable);
        $parentRole = $this->roleRepository->findOneByUuid($dto->parent);

        $entity->setUuid($this->getUuid($dto->uuid))
            ->setName(strtoupper($dto->name))
            ->setTranslatable($translatable)
            ->setParent($parentRole);

        return $entity;
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
        /* @var Role $entity */
        /* @var RoleDto $dto */
        $dto->uuid = $entity->getUuid();
        $dto->name = $entity->getName();
        foreach ($entity->getTranslatable()->getTranslations() as $translation) {
            $dto->translatable[] = [
                'message' => $translation->getMessage(),
                'language' => $translation->getLanguage()->getUuid(),
            ];
        }
        if ($parentRole = $entity->getParent()) {
            $dto->parent = $parentRole->getUuid();
        }


        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Role;
    }
}
