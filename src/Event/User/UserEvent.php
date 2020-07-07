<?php

namespace App\Event\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'user.pre.create';
    public const CREATE = 'user.create';
    public const POST_CREATE = 'user.post.create';
    public const PRE_UPDATE = 'user.pre.update';
    public const UPDATE = 'user.update';
    public const POST_UPDATE = 'user.post.update';
    public const PRE_DELETE = 'user.pre.delete';
    public const DELETE = 'user.delete';
    public const POST_DELETE = 'user.post.delete';

    private $relatedEntity = 'User';

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
     * @return array|string[]
     */
    public static function getEventsName(): array
    {
        return [
            self::PRE_CREATE,
            self::CREATE,
            self::POST_CREATE,
            self::PRE_UPDATE,
            self::UPDATE,
            self::POST_UPDATE,
            self::PRE_DELETE,
            self::DELETE,
            self::POST_DELETE,
        ];
    }

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
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
