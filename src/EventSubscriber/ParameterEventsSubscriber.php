<?php

namespace App\EventSubscriber;

use App\Event\Parameter\ParameterEvent;
use App\Exception\HydratorException;
use App\Service\Parameter\ParameterService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParameterEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterService
     */
    private $parameterService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param ParameterService $parameterService
     */
    public function __construct(ParameterService $parameterService)
    {
        $this->parameterService = $parameterService;
    }

    public static function getSubscribedEvents()
    {
        return [
            ParameterEvent::CREATE => ['onParameterCreate', 0],
            ParameterEvent::UPDATE => ['onParameterUpdate', 0],
            ParameterEvent::DELETE => ['onParameterDelete', 0],
            ParameterEvent::SOFT_DELETE => ['onParameterSoftDelete', 0],
        ];
    }

    /**
     * @param ParameterEvent $event
     *
     * @throws HydratorException
     */
    public function onParameterCreate(ParameterEvent $event)
    {
        $this->parameterService->createParameterFromDto($event->getParameterDto());
    }

    /**
     * @param ParameterEvent $event
     *
     * @throws HydratorException
     */
    public function onParameterUpdate(ParameterEvent $event)
    {
        $this->parameterService->updateParameterFromDto($event->getParameterDto(), $event->getParameter());
    }

    /**
     * @param ParameterEvent $event
     *
     * @throws ReflectionException
     */
    public function onParameterDelete(ParameterEvent $event)
    {
        $this->parameterService->deleteParameter($event->getParameter());
    }

    /**
     * @param ParameterEvent $event
     *
     * @throws ReflectionException
     */
    public function onParameterSoftDelete(ParameterEvent $event)
    {
        $this->parameterService->deleteSoftParameter($event->getParameter());
    }
}
