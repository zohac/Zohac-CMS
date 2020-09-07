<?php

namespace App\Event\Maintenance;

use App\Entity\Maintenance;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class MaintenanceViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const ENTITY_NAME = Maintenance::class;

    public const CREATE = 'maintenance.create.view';
    public const UPDATE = 'maintenance.update.view';
    public const DELETE = 'maintenance.delete.view';
    public const LIST = 'maintenance.list.view';
    public const DETAIL = 'maintenance.detail.view';

    /**
     * @return array|string[]
     */
    public static function getEventsName(): array
    {
        return [
            self::CREATE,
            self::DELETE,
            self::UPDATE,
            self::DETAIL,
            self::LIST,
        ];
    }

    /**
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return self::ENTITY_NAME;
    }
}
