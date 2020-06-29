<?php

namespace App\Service;

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
}
