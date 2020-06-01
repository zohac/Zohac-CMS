<?php

namespace App\Event\User;

use App\Dto\user\UserDto;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserPreCreateEvent extends Event implements EventInterface
{
    use EventTrait;

    public const NAME = 'user.pre.create';

    /**
     * @var UserDto
     */
    private $userDto;

    /**
     * @var FormInterface
     */
    private $form;

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
}
