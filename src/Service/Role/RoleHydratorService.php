<?php

namespace App\Service\Role;

use App\Dto\Role\RoleDto;
use App\Entity\Role;
use App\Entity\Translatable;
use App\Entity\Translation;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;

class RoleHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * RoleHydratorService constructor.
     *
     * @param UuidService $uuidService
     */
    public function __construct(UuidService $uuidService)
    {
        $this->uuidService = $uuidService;
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
        /**
         * @var Role $entity
         * @var RoleDto $dto
         */

        $uuid = (null !== $dto->uuid) ? $dto->uuid : $this->getUuid();

        $translatable = ($entity->getTranslatable() instanceof Translatable) ?
            $entity->getTranslatable() :
            (new Translatable())->setUuid($this->getUuid());

        /** @var Translation $translation */
        foreach ($dto->translatable as $translation) {
            if (null === $translation->getUuid()) {
                $translation->setUuid($this->getUuid());
                $translatable->addTranslation($translation);
            }
        }

        $entity->setUuid($uuid)
            ->setName(strtoupper($dto->name))
            ->setTranslatable($translatable);

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
        /**
         * @var Role $entity
         * @var RoleDto $dto
         */

        $dto->uuid = $entity->getUuid();
        $dto->name = $entity->getName();
        foreach ($entity->getTranslatable()->getTranslations() as $translation) {
            $dto->translatable[] = $translation;
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
