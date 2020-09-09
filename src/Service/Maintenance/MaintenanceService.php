<?php

namespace App\Service\Maintenance;

use App\Dto\Maintenance\MaintenanceDto;
use App\Entity\Maintenance;
use App\Event\Maintenance\MaintenanceEvent;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Repository\MaintenanceRepository;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class MaintenanceService implements ServiceInterface
{
    const MAINTENANCE = 'maintenance';

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var MaintenanceRepository
     */
    private $repository;

    /**
     * MaintenanceService constructor.
     * @param EventService $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService $entityService
     * @param MaintenanceRepository $repository
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService,
        MaintenanceRepository $repository
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
        $this->repository = $repository;
    }

    /**
     * @param MaintenanceDto $maintenanceDto
     *
     * @return Maintenance
     *
     * @throws EventException
     * @throws HydratorException
     */
    public function createMaintenanceFromDto(MaintenanceDto $maintenanceDto): Maintenance
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->entityService->hydrateEntityWithDto(new Maintenance(), $maintenanceDto);

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_CREATE, [
            self::MAINTENANCE => $maintenance,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            ucfirst(self::MAINTENANCE),
            'Maintenance successfully created.',
            self::MAINTENANCE
        );

        return $maintenance;
    }

    /**
     * @param MaintenanceDto $maintenanceDto
     * @param Maintenance    $maintenance
     *
     * @return Maintenance
     *
     * @throws EventException
     * @throws HydratorException
     */
    public function updateMaintenanceFromDto(MaintenanceDto $maintenanceDto, Maintenance $maintenance): Maintenance
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->entityService->hydrateEntityWithDto($maintenance, $maintenanceDto);

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_UPDATE, [
            self::MAINTENANCE => $maintenance,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            ucfirst(self::MAINTENANCE),
            'Maintenance successfully updated.',
            self::MAINTENANCE
        );

        return $maintenance;
    }

    /**
     * @param Maintenance $maintenance
     *
     * @return $this
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function deleteMaintenance(Maintenance $maintenance): self
    {
        $this->entityService
            ->setEntity($maintenance)
            ->remove($maintenance)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            ucfirst(self::MAINTENANCE),
            'Maintenance successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param Maintenance $maintenance
     *
     * @return $this
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function deleteSoftMaintenance(Maintenance $maintenance)
    {
        $maintenance->setArchived(true);

        $this->entityService
            ->setEntity($maintenance)
            ->persist($maintenance)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            ucfirst(self::MAINTENANCE),
            'Maintenance successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getDeleteMessage(EntityInterface $entity): string
    {
        /* @var Maintenance $entity */
        return $this->flashBagService->trans(
            'Are you sure you want to delete this maintenance (%maintenance%) ?',
            self::MAINTENANCE,
            [self::MAINTENANCE => $entity->getUuid()]
        );
    }

    public function isInMaintenance(): bool
    {
        return false;
    }
}
