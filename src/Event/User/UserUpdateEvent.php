<?php

namespace App\Event\User;

use App\Dto\user\UserDto;
use App\Entity\User;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Contracts\EventDispatcher\Event;

class UserUpdateEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.update';

    /**
     * @var UserDto
     */
    private $userDto;

    /**
     * @var User
     */
    private $user;

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
