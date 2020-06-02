<?php

namespace App\Event\User;

use App\Entity\User;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserPostUpdateEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.post.update';

    /**
     * @var User
     */
    private $user;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
