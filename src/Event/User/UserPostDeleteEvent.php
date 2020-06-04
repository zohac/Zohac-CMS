<?php

namespace App\Event\User;

use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserPostDeleteEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.post.delete';
}
