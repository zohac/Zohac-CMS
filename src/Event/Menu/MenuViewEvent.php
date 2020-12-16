<?php

namespace App\Event\Menu;

use App\Entity\Menu;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class MenuViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const ENTITY_NAME = Menu::class;

    public const CREATE = 'menu.create.view';
    public const UPDATE = 'menu.update.view';
    public const DELETE = 'menu.delete.view';
    public const LIST = 'menu.list.view';
    public const DETAIL = 'menu.detail.view';

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
