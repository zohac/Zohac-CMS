<?php

namespace App\Event\Role;

use App\Dto\Role\RoleDto;
use App\Entity\Role;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RoleEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'role.pre.create';
    public const CREATE = 'role.create';
    public const POST_CREATE = 'role.post.create';
    public const PRE_UPDATE = 'role.pre.update';
    public const UPDATE = 'role.update';
    public const POST_UPDATE = 'role.post.update';
    public const PRE_DELETE = 'role.pre.delete';
    public const DELETE = 'role.delete';
    public const SOFT_DELETE = 'role.soft.delete';
    public const POST_DELETE = 'role.post.delete';

    const ENTITY_NAME = Role::class;

    /**
     * @var RoleDto
     */
    private $roleDto;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Role
     */
    private $role;

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
     * @return RoleDto
     */
    public function getRoleDto(): RoleDto
    {
        return $this->roleDto;
    }

    /**
     * @param RoleDto $roleDto
     *
     * @return $this
     */
    public function setRoleDto(RoleDto $roleDto): self
    {
        $this->roleDto = $roleDto;

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
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return self::ENTITY_NAME;
    }
}
