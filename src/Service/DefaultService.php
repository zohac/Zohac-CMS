<?php

namespace App\Service;

use App\Interfaces\Event\EventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultService
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(
        SerializerInterface $serializer,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        EventService $eventService
    ) {
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
        $this->eventService = $eventService;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @param string $eventName
     *
     * @return EventInterface
     */
    public function getEvent(string $eventName): EventInterface
    {
        return $this->eventService->getEvent($eventName);
    }

    /**
     * @param string $eventName
     * @param array|null $data
     *
     * @return $this
     */
    public function dispatchEvent(string $eventName, ?array $data = []): self
    {
        $event = $this->getEvent($eventName);
        $event->setData($data);
        $this->getEventDispatcher()->dispatch($event);

        return $this;
    }
}
