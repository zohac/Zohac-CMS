<?php

namespace App\Traits\Event;

trait EventTrait
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $eventCalled;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     *
     * @return $this
     */
    public function setData(?array $data = []): self
    {
        $this->data = $data;

        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return static::class;
    }

    /**
     * @return string
     */
    public function getEventCalled(): string
    {
        return $this->eventCalled;
    }

    /**
     * @param string $eventCalled
     *
     * @return $this
     */
    public function setEventCalled(string $eventCalled): self
    {
        $this->eventCalled = $eventCalled;

        return $this;
    }
}
