<?php

namespace App\Service\Menu;

use App\Dto\Menu\MenuDto;
use App\Entity\Menu;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;

class MenuHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * MenuHydratorService constructor.
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
     *
     * @var Menu    $entity
     * @var MenuDto $dto
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /* @var Menu $entity */
        /* @var MenuDto $dto */

        $entity->setUuid($this->getUuid($dto->uuid))
            ->setItems($dto->items)
            ->setArchived($dto->archived)
        ;

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
     *
     * @var Menu
     * @var MenuDto $dto
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $dto->items = $entity->getItems();
        $dto->uuid = $entity->getUuid();
        $dto->archived = $entity->getArchived();

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Menu;
    }
}
