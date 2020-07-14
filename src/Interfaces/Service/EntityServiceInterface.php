<?php

namespace App\Interfaces\Service;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Service\EventService;

interface EntityServiceInterface
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
    public function getEntityShortName(): string;

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

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface;

    /**
     * @return EventService
     */
    public function getEventService(): EventService;

    /**
     * @param string $eventName
     *
     * @return string
     */
    public function getEvent(string $eventName): string;

    /**
     * @param string $eventName
     *
     * @return string
     */
    public function getViewEvent(string $eventName): string;

    /**
     * @return string
     */
    public function getDeleteMessage(): string;
}
