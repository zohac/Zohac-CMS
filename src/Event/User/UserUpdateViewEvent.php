<?php

namespace App\Event\User;

use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Traits\Event\EventTrait;
use App\Traits\Event\ViewEventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserUpdateViewEvent extends Event implements EventInterface, ViewEventInterface
{
    use EventTrait;
    use ViewEventTrait;

    public const NAME = 'user.update.view';
}
