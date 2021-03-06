<?php

namespace App\Service;

use App\Interfaces\Event\ViewEventInterface;

class ViewService
{
    const NAME = 'viewService';

    /**
     * @var string
     */
    private $view = null;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var ViewEventInterface[]
     */
    private $viewEvents;

    /**
     * ViewService constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->viewEvents[$handler->getRelatedEntity()] = $handler;
        }
    }

    /**
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function addOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param string $view
     * @param array  $options
     *
     * @return $this
     */
    public function setData(string $view, array $options = []): self
    {
        $this->view = $view;
        $this->options = $options;

        return $this;
    }

    /**
     * @return ViewEventInterface[]
     */
    public function getViewEvents(): array
    {
        return $this->viewEvents;
    }
}
