<?php

namespace App\EventSubscriber;

use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Service\FinderService;
use function array_slice;
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

    public function __construct(LoggerInterface $appLogger)
    {
        $this->logger = $appLogger;
    }

    public static function getSubscribedEvents()
    {
        $subscribedEvents = [
            KernelEvents::EXCEPTION => 'onException',
        ];
        $events = FinderService::getEventsByInterface(EventInterface::class, true);
        $viewEvents = FinderService::getEventsByInterface(ViewEventInterface::class);

        foreach ($viewEvents as $viewEvent) {
            $subscribedEvents[$viewEvent] = 'onViewEvent';
        }

        foreach ($events as $event) {
            $subscribedEvents[$event] = 'onEvent';
        }

        return $subscribedEvents;
    }

    /**
     * @param ViewEventInterface $event
     */
    public function onViewEvent(ViewEventInterface $event)
    {
        $this->logger->info($event->getEventCalled(), $this->getContext());
    }

    /**
     * @param EventInterface $event
     */
    public function onEvent(EventInterface $event)
    {
        $this->logger->info($event->getEventCalled(), $this->getContext());
    }

    public function onException(ExceptionEvent $event)
    {
        dump($event);
        $this->logger->error($event->getThrowable()->getMessage(), [$event->getThrowable()]);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return debug_backtrace();
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return array
     */
    public function getExceptionContext(ExceptionEvent $event): array
    {
        $line = $event->getThrowable()->getLine();
        $message = $event->getThrowable()->getMessage();
        $file = $event->getThrowable()->getFile();

        $backtrace = debug_backtrace();
        // Clean the trace by removing first frames added by the error handler itself.
        for ($i = 0; isset($backtrace[$i]); ++$i) {
            if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                $backtrace = array_slice($backtrace, 1 + $i);
                break;
            }
        }

        $collectedLogs[$message] = [
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => [$backtrace[0]],
            'count' => 1,
        ];

        return $collectedLogs;
    }
}
