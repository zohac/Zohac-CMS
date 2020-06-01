<?php

namespace App\Interfaces\Event;

interface EventInterface
{
    /**
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
}
