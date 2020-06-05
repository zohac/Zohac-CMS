<?php

namespace App\EventSubscriber;

use App\Event\User\UserViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserViewEvent::CREATE => 'onViewEvent',
            UserViewEvent::UPDATE => 'onViewEvent',
            UserViewEvent::DELETE => 'onViewEvent',
            UserViewEvent::DETAIL => 'onViewEvent',
            UserViewEvent::LIST => 'onViewEvent',
        ];
    }

    public function onViewEvent(UserViewEvent $event)
    {
        $this->logger->info($event->getEventCalled(), $this->getContext());
    }

    public function getContext()
    {
        return debug_backtrace();
    }
}
