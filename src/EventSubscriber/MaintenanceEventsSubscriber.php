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
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MaintenanceEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var MaintenanceService
     */
    private $maintenanceService;

    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * MaintenanceEventsSubscriber constructor.
     *
     * @param MaintenanceService $maintenanceService
     */
    public function __construct(MaintenanceService $maintenanceService, Environment $twigEnvironment)
    {
        $this->maintenanceService = $maintenanceService;
        $this->twigEnvironment = $twigEnvironment;
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onMaintenance(RequestEvent $event)
    {
        if (
            $this->maintenanceService->isInMaintenance() &&
            !$this->maintenanceService->isAuthorizedPath($event->getRequest()) &&
            !$this->maintenanceService->isAuthorizedIP($event->getRequest())
        ) {
            $template = $this->twigEnvironment->render('maintenance.html.twig');

            $event->setResponse(new Response($template, Response::HTTP_SERVICE_UNAVAILABLE));
            $event->stopPropagation();
        }
    }
}
