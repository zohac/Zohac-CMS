<?php

namespace App\Event\User;

use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const CREATE = 'user.create.view';
    public const UPDATE = 'user.update.view';
    public const DELETE = 'user.delete.view';
    public const LIST = 'user.list.view';
    public const DETAIL = 'user.detail.view';

    private $relatedEntity = 'User';

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
        return $this->relatedEntity;
    }
}
