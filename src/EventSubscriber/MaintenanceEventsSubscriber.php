<?php

namespace App\EventSubscriber;

use App\Event\Maintenance\MaintenanceEvent;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Exception\MaintenanceException;
use App\Service\Maintenance\MaintenanceResponseService;
use App\Service\Maintenance\MaintenanceService;
use App\Service\ResponseService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Error as TwigError;

class MaintenanceEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var MaintenanceService
     */
    private $maintenanceService;

    /**
     * @var ResponseService
     */
    private $responseService;

    /**
     * MaintenanceEventsSubscriber constructor.
     *
     * @param MaintenanceService         $maintenanceService
     * @param MaintenanceResponseService $responseService
     */
    public function __construct(MaintenanceService $maintenanceService, MaintenanceResponseService $responseService)
    {
        $this->maintenanceService = $maintenanceService;
        $this->responseService = $responseService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MaintenanceEvent::CREATE => ['onMaintenanceCreate', 0],
            MaintenanceEvent::UPDATE => ['onMaintenanceUpdate', 0],
            MaintenanceEvent::DELETE => ['onMaintenanceDelete', 0],
            MaintenanceEvent::SOFT_DELETE => ['onMaintenanceSoftDelete', 0],
            KernelEvents::REQUEST => ['onMaintenance', 0],
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
     * @throws TwigError\LoaderError
     * @throws TwigError\RuntimeError
     * @throws TwigError\SyntaxError
     */
    public function onMaintenance(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($this->maintenanceService->isNotAuthorized($request->getClientIp(), $request->getRequestUri())) {
            $response = $this->responseService->getResponse('maintenance.html.twig', $request->getRequestUri());

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
