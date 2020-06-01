<?php

namespace App\Traits\Event;

use App\Service\ViewService;

trait ViewEventTrait
{
    /**
     * @var ViewService
     */
    private $viewService;

    /**
     * @return ViewService
     */
    public function getViewService(): ViewService
    {
        return $this->viewService;
    }

    /**
     * @param ViewService $viewService
     *
     * @return $this
     */
    public function setViewService(ViewService $viewService): self
    {
        $this->viewService = $viewService;

        return $this;
    }
}
