<?php

namespace App\Interfaces\Event;

interface EventInterface
{
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
     * Return an array of all the events name.
     *
     * @return array
     */
    public function getEventsName();
}
