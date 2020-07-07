<?php

namespace App\Service;

use App\Exception\EventException;
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
    public function setOptions(array $options): self
    {
        $this->options = $options;

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

    /**
     * @param string $entityName
     * @param string $constantName
     * @return string
     */
    public function getConstant(string $entityName, string $constantName): string
    {
        $constantName = strtoupper($constantName);

        if (defined($this->viewEvents[ucfirst($entityName)]::$constantName)) {
            return $this->viewEvents[ucfirst($entityName)]::$constantName;
        }
        if (defined($this->viewEvents[strtoupper($entityName)]::$constantName)) {
            return $this->viewEvents[strtoupper($entityName)]::$constantName;
        }

        if (defined($this->viewEvents[strtolower($entityName)]::$constantName)) {
            return $this->viewEvents[strtolower($entityName)]::$constantName;
        }

        throw new EventException('La constante de class demandé n\'existe pas.');
    }
}
