<?php

namespace App\Service;

use App\Exception\EventException;
use App\Interfaces\Event\EventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventService
{
    /**
     * @var EventInterface[]
     */
    private $events;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * EventService constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers, EventDispatcherInterface $eventDispatcher)
    {
        foreach ($handlers as $handler) {
            $this->events[] = $handler;
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $eventName
     *
     * @return EventInterface
     *
     * @throws EventException
     */
    public function getEvent(string $eventName)
    {
        foreach ($this->events as $event) {
            if (in_array($eventName, $event->getEventsName())) {
                $event->setEventCalled($eventName);

                return $event;
            }
        }

        throw new EventException(sprintf('Le nom de l\'event : %s n\'existe pas.', $eventName));
    }

    /**
     * @return EventInterface[]|array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @param string     $eventName
     * @param array|null $data
     *
     * @return $this
     */
    public function dispatchEvent(string $eventName, ?array $data = []): self
    {
        $event = $this->getEvent($eventName);
        $event->setData($data);
        $this->eventDispatcher->dispatch($event, $eventName);

        return $this;
    }
}
