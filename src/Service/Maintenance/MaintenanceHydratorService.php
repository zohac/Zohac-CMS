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
        /* @var Maintenance $entity */
        /* @var MaintenanceDto $dto */
        $mode = (empty($dto->mode)) ? false : true;

        $entity->setUuid($this->getUuid($dto->uuid))
            ->setRedirectpath($dto->redirectPath)
            ->setMode($mode)
            ->setIps($dto->ips);

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
        /* @var Maintenance $entity */
        /* @var MaintenanceDto $dto */
        $dto->redirectPath = $entity->getRedirectpath();
        $dto->mode = $entity->getMode();
        $dto->ips = $entity->getIps();
        $dto->uuid = $entity->getUuid();

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
