<?php

namespace App\Event\User;

use App\Dto\user\UserDto;
use App\Entity\User;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserPreUpdateEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.pre.update';

    /**
     * @var UserDto
     */
    private $userDto;

    /**
     * @var FormInterface
     */
    private $form;

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
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     *
     * @return $this
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

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
