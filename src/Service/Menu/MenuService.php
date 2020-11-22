<?php

namespace App\Service\Menu;

use App\Dto\Menu\MenuDto;
use App\Entity\Menu;
use App\Event\Menu\MenuEvent;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class MenuService implements ServiceInterface
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * MenuService constructor.
     *
     * @param EventService    $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService   $entityService
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
    }

    /**
     * @param MenuDto $menuDto
     *
     * @return Menu
     *
     * @throws HydratorException
     */
    public function createMenuFromDto(MenuDto $menuDto): Menu
    {
        /** @var Menu $menu */
        $menu = $this->entityService->hydrateEntityWithDto(new Menu(), $menuDto);

        $this->eventService->dispatchEvent(MenuEvent::POST_CREATE, [
            'menu' => $menu,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Menu',
            'Menu successfully created.',
            'menu'
        );

        return $menu;
    }

    /**
     * @param MenuDto $menuDto
     * @param Menu    $menu
     *
     * @return Menu
     *
     * @throws HydratorException
     */
    public function updateMenuFromDto(MenuDto $menuDto, Menu $menu): Menu
    {
        /** @var Menu $menu */
        $menu = $this->entityService->hydrateEntityWithDto($menu, $menuDto);

        $this->eventService->dispatchEvent(MenuEvent::POST_UPDATE, [
            'menu' => $menu,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Menu',
            'Menu successfully updated.',
            'menu'
        );

        return $menu;
    }

    /**
     * @param Menu $menu
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteMenu(Menu $menu): self
    {
        $this->entityService
            ->setEntity($menu)
            ->remove($menu)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Menu',
            'Menu successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(MenuEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param Menu $menu
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteSoftMenu(Menu $menu)
    {
        $menu->setArchived(true);

        $this->entityService
            ->setEntity($menu)
            ->persist($menu)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Menu',
            'Menu successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(MenuEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getDeleteMessage(EntityInterface $entity): string
    {
        // TODO: Change the Delete Message

        /* @var Menu $entity */
        //  return $this->flashBagService->trans(
        //      'Are you sure you want to delete this menu (%menu%) ?',
        //      'menu',
        //      ['menu' => $entity->getName()]
        //  );

        return 'Are you sure you want to delete this menu  ?';
    }
}
