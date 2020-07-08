<?php

namespace App\Interfaces\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Service\EventService;

interface ServiceInterface
{
    /**
     * @return string
     */
    public function getFormType(): string;

    /**
     * @return string
     */
    public function getEntityName(): string;

    /**
     * @return DtoInterface
     */
    public function getDto(): DtoInterface;

    /**
     * @return EventService
     */
    public function getEventService(): EventService;
}
