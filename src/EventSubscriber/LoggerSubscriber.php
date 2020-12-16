<?php

namespace App\EventSubscriber;

use App\Interfaces\Event\EventInterface;
use App\Service\DebugService;
use App\Service\FinderService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DebugService
     */
    private $debugService;

    /**
     * LoggerSubscriber constructor.
     *
     * @param LoggerInterface $appLogger
     * @param DebugService    $debugService
     */
    public function __construct(LoggerInterface $appLogger, DebugService $debugService)
    {
        $this->logger = $appLogger;
        $this->debugService = $debugService;
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents(): array
    {
//        $subscribedEvents = [
//            KernelEvents::EXCEPTION => 'onException',
//        ];
//        $events = FinderService::getEventsByInterface(EventInterface::class);
//
//        foreach ($events as $event) {
//            $subscribedEvents[$event] = 'onEvent';
//        }
//
//        return $subscribedEvents;

        return [];
    }

    /**
     * @param EventInterface $event
     */
    public function onEvent(EventInterface $event)
    {
        $this->logger->debug($event->getEventCalled(), $this->debugService->getContext());
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {
        $this->debugService->displayDebugMessage('error');
        $this->logger->error($event->getThrowable()->getMessage(), [$event->getThrowable()]);
    }
}
