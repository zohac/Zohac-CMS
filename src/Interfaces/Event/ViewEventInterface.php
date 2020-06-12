<?php

namespace App\Interfaces\Event;

use App\Service\ViewService;

interface ViewEventInterface
{
    /**
     * @return ViewService
     */
    public function getViewService();

    /**
     * @param ViewService $viewService
     *
     * @return $this
     */
    public function setViewService(ViewService $viewService);

    /**
     * @return string
     */
    public function getEventCalled(): string;
}
