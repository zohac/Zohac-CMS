<?php

namespace App\Interfaces\Event;

interface EventInterface
{
    /**
     * Return an array of all the events name.
     *
     * @return array
     */
    public static function getEventsName();

    /**
     * Set the raw data in the event.
     *
     * @param array $data
     *
     * @return self
     */
    public function setData(array $data);

    /**
     * Return the name of the class.
     *
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getEventCalled(): string;

    /**
     * @param string $eventName
     *
     * @return $this
     */
    public function setEventCalled(string $eventName);
}
