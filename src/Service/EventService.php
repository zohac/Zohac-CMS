<?php

namespace App\Service;

use App\Exception\EventException;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventService
{
    /**
     * @var EventInterface[]
     */
    private $allEvents;

    /**
     * @var EventInterface[]
     */
    private $events;

    /**
     * @var ViewEventInterface[]
     */
    private $viewEvents;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * EventService constructor.
     *
     * @param iterable                 $handlers
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(iterable $handlers, EventDispatcherInterface $eventDispatcher)
    {
        foreach ($handlers as $handler) {
            $this
                ->sortByViewEventInterface($handler)
                ->sortByEventInterface($handler);

            $this->allEvents[] = $handler;
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $handler
     *
     * @return $this
     */
    public function sortByEventInterface($handler): self
    {
        if (($handler instanceof EventInterface) && !($handler instanceof ViewEventInterface)) {
            $this->events[$handler->getRelatedEntity()] = $handler;
        }

        return $this;
    }

    /**
     * @param $handler
     *
     * @return $this
     */
    public function sortByViewEventInterface($handler): self
    {
        if ($handler instanceof ViewEventInterface) {
            $this->viewEvents[$handler->getRelatedEntity()] = $handler;
        }

        return $this;
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
     *
     * @throws EventException
     */
    public function dispatchEvent(string $eventName, ?array $data = []): self
    {
        $event = $this->getEvent($eventName);
        $event->setData($data);
        $this->eventDispatcher->dispatch($event, $eventName);

        return $this;
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
        foreach ($this->allEvents as $event) {
            if (in_array($eventName, $event->getEventsName())) {
                $event->setEventCalled($eventName);

                return $event;
            }
        }

        throw new EventException(sprintf('Le nom de l\'event : %s n\'existe pas.', $eventName));
    }

    /**
     * @return ViewEventInterface[]
     */
    public function getViewEvents(): array
    {
        return $this->viewEvents;
    }
}
