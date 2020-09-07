<?php

namespace App\Service\Maintenance;

use App\Dto\Maintenance\MaintenanceDto;
use App\Entity\Maintenance;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;

class MaintenanceHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * MaintenanceHydratorService constructor.
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
    * @var Maintenance    $entity
    * @var MaintenanceDto $dto
    */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /** @var Maintenance $entity */
        /** @var MaintenanceDto $dto */

        $entity->setUuid($this->getUuid($dto->uuid))
            ->setRedirectpath($dto->redirectPath)
            ->setMode($dto->mode)
            ->setIps($dto->ips)
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
    * @var Maintenance
    * @var MaintenanceDto $dto
    */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $dto->redirectPath = $entity->getRedirectpath();
        $dto->mode = $entity->getMode();
        $dto->ips = $entity->getIps();
        $dto->uuid = $entity->getUuid();
        $dto->archived = $entity->getArchived();

        return $dto;
    }

    /**
    * {@inheritdoc}
    */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Maintenance;
    }
}
