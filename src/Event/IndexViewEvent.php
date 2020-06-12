<?php

namespace App\Event;

use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class IndexViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const INDEX = 'index.view';

    /**
     * @return array|string[]
     */
    public static function getEventsName(): array
    {
        return [
            self::INDEX,
        ];
    }
}
