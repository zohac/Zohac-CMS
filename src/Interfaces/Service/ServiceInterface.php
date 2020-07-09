<?php

namespace App\Interfaces\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
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
     * @return string
     */
    public function getEntityNameToLower(): string;

    /**
     * @return string
     */
    public function getEntityNamePlural(): string;

    /**
     * @return DtoInterface
     */
    public function getDto(): DtoInterface;


    public function getEntity(): EntityInterface;

    /**
     * @return EventService
     */
    public function getEventService(): EventService;

    /**
     * @return EventInterface
     */
    public function getEvent(): EventInterface;

    /**
     * @return ViewEventInterface
     */
    public function getViewEvent(): ViewEventInterface;
}
