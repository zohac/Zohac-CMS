<?php

namespace App\Event\Role;

use App\Entity\Role;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class RoleViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const ENTITY_NAME = Role::class;

    public const CREATE = 'role.create.view';
    public const UPDATE = 'role.update.view';
    public const DELETE = 'role.delete.view';
    public const LIST = 'role.list.view';
    public const DETAIL = 'role.detail.view';

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
