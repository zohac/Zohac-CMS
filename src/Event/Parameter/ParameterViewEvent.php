<?php

namespace App\Event\Parameter;

use App\Entity\Parameter;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class ParameterViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const ENTITY_NAME = Parameter::class;

    public const CREATE = 'parameter.create.view';
    public const UPDATE = 'parameter.update.view';
    public const DELETE = 'parameter.delete.view';
    public const LIST = 'parameter.list.view';
    public const DETAIL = 'parameter.detail.view';

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
