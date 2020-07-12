<?php

namespace App\Event\Language;

use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class LanguageViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const RELATED_ENTITY = 'Language';

    public const CREATE = 'language.create.view';
    public const UPDATE = 'language.update.view';
    public const DELETE = 'language.delete.view';
    public const LIST = 'language.list.view';
    public const DETAIL = 'language.detail.view';

    private $relatedEntity = 'Language';

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
        return self::RELATED_ENTITY;
    }
}
