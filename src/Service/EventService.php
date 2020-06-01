<?php

namespace App\Service;

use App\Exception\EventException;
use App\Interfaces\Event\EventInterface;

class EventService
{
    /**
     * @var EventInterface[]
     */
    private $events;

    /**
     * EventService constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->events[] = $handler;
        }
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
            if ($event::NAME === $eventName) {
                return $event;
            }
        }

        throw new EventException('Le nom de l\'event n\'existe pas.');
    }
}
