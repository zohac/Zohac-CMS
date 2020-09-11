<?php

namespace App\EventSubscriber;

use App\Event\Maintenance\MaintenanceEvent;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Exception\MaintenanceException;
use App\Service\Maintenance\MaintenanceService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MaintenanceEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var MaintenanceService
     */
    private $maintenanceService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param MaintenanceService $maintenanceService
     */
    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MaintenanceEvent::CREATE => ['onMaintenanceCreate', 0],
            MaintenanceEvent::UPDATE => ['onMaintenanceUpdate', 0],
            MaintenanceEvent::DELETE => ['onMaintenanceDelete', 0],
            MaintenanceEvent::SOFT_DELETE => ['onMaintenanceSoftDelete', 0],
            'kernel.request' => ['onMaintenance', 0],
        ];
    }

    /**
     * @param MaintenanceEvent $event
     *
     * @throws EventException
     * @throws HydratorException
     */
    public function onMaintenanceCreate(MaintenanceEvent $event)
    {
        $this->maintenanceService->createMaintenanceFromDto($event->getMaintenanceDto());
    }

    /**
     * @param MaintenanceEvent $event
     *
     * @throws EventException
     * @throws HydratorException
     */
    public function onMaintenanceUpdate(MaintenanceEvent $event)
    {
        $this->maintenanceService->updateMaintenanceFromDto($event->getMaintenanceDto(), $event->getMaintenance());
    }

    /**
     * @param MaintenanceEvent $event
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function onMaintenanceDelete(MaintenanceEvent $event)
    {
        $this->maintenanceService->deleteMaintenance($event->getMaintenance());
    }

    /**
     * @param MaintenanceEvent $event
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function onMaintenanceSoftDelete(MaintenanceEvent $event)
    {
        $this->maintenanceService->deleteSoftMaintenance($event->getMaintenance());
    }

    /**
     * @param RequestEvent $event
     *
     * @throws MaintenanceException
     */
    public function onMaintenance(RequestEvent $event)
    {
        if ($this->maintenanceService->isInMaintenance()) {
            dump($event);

            $event->setResponse(
                new Response(
                    'site is in maintenance mode',
                    Response::HTTP_SERVICE_UNAVAILABLE
                )
            );
            $event->stopPropagation();
        }
    }
}
