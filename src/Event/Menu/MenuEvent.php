<?php

namespace App\Event\Menu;

use App\Dto\Menu\MenuDto;
use App\Entity\Menu;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MenuEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'menu.pre.create';
    public const CREATE = 'menu.create';
    public const POST_CREATE = 'menu.post.create';
    public const PRE_UPDATE = 'menu.pre.update';
    public const UPDATE = 'menu.update';
    public const POST_UPDATE = 'menu.post.update';
    public const PRE_DELETE = 'menu.pre.delete';
    public const DELETE = 'menu.delete';
    public const SOFT_DELETE = 'menu.soft.delete';
    public const POST_DELETE = 'menu.post.delete';

    const ENTITY_NAME = Menu::class;

    /**
     * @var MenuDto
     */
    private $menuDto;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Menu
     */
    private $menu;

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
     * @return MenuDto
     */
    public function getMenuDto(): MenuDto
    {
        return $this->menuDto;
    }

    /**
     * @param MenuDto $menuDto
     *
     * @return $this
     */
    public function setMenuDto(MenuDto $menuDto): self
    {
        $this->menuDto = $menuDto;

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
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     *
     * @return $this
     */
    public function setMenu(Menu $menu): self
    {
        $this->menu = $menu;

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
