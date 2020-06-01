<?php

namespace App\Event\User;

use App\Dto\user\UserDto;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserCreateEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.create';

    /**
     * @var UserDto
     */
    private $userDto;

    /**
     * @return UserDto
     */
    public function getUserDto(): UserDto
    {
        return $this->userDto;
    }

    /**
     * @param UserDto $userDto
     *
     * @return $this
     */
    public function setUserDto(UserDto $userDto): self
    {
        $this->userDto = $userDto;

        return $this;
    }
}
