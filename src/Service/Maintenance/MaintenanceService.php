<?php

namespace App\Service\Maintenance;

use App\Dto\Maintenance\MaintenanceDto;
use App\Entity\Maintenance;
use App\Event\Maintenance\MaintenanceEvent;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class MaintenanceService implements ServiceInterface
{
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
     * MaintenanceService constructor.
     *
     * @param EventService    $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService   $entityService
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
    }

    /**
     * @param MaintenanceDto $maintenanceDto
     *
     * @return Maintenance
     *
     * @throws HydratorException
     */
    public function createMaintenanceFromDto(MaintenanceDto $maintenanceDto): Maintenance
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->entityService->hydrateEntityWithDto(new Maintenance(), $maintenanceDto);

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_CREATE, [
            'maintenance' => $maintenance,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Maintenance',
            'Maintenance successfully created.',
            'maintenance'
        );

        return $maintenance;
    }

    /**
     * @param MaintenanceDto $maintenanceDto
     * @param Maintenance    $maintenance
     *
     * @return Maintenance
     *
     * @throws HydratorException
     */
    public function updateMaintenanceFromDto(MaintenanceDto $maintenanceDto, Maintenance $maintenance): Maintenance
    {
        /** @var Maintenance $maintenance */
        $maintenance = $this->entityService->hydrateEntityWithDto($maintenance, $maintenanceDto);

        $this->eventService->dispatchEvent(MaintenanceEvent::POST_UPDATE, [
            'maintenance' => $maintenance,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Maintenance',
            'Maintenance successfully updated.',
            'maintenance'
        );

        return $maintenance;
    }

    /**
     * @param Maintenance $maintenance
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteMaintenance(Maintenance $maintenance): self
    {
        $this->entityService
            ->setEntity($maintenance)
            ->remove($maintenance)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Maintenance',
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
     */
    public function deleteSoftMaintenance(Maintenance $maintenance)
    {
        $maintenance->setArchived(true);

        $this->entityService
            ->setEntity($maintenance)
            ->persist($maintenance)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Maintenance',
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
        // TODO: Change the Delete Message

        /* @var Maintenance $entity */
        //  return $this->flashBagService->trans(
        //      'Are you sure you want to delete this maintenance (%maintenance%) ?',
        //      'maintenance',
        //      ['maintenance' => $entity->getName()]
        //  );

        return 'Are you sure you want to delete this maintenance  ?';
    }
}
