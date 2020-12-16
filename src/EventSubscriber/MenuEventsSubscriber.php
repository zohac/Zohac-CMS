<?php

namespace App\EventSubscriber;

use App\Event\Menu\MenuEvent;
use App\Exception\HydratorException;
use App\Service\Menu\MenuService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var MenuService
     */
    private $menuService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param MenuService $menuService
     */
    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MenuEvent::CREATE => ['onMenuCreate', 0],
            MenuEvent::UPDATE => ['onMenuUpdate', 0],
            MenuEvent::DELETE => ['onMenuDelete', 0],
            MenuEvent::SOFT_DELETE => ['onMenuSoftDelete', 0],
        ];
    }

    /**
     * @param MenuEvent $event
     *
     * @throws HydratorException
     */
    public function onMenuCreate(MenuEvent $event)
    {
        $this->menuService->createMenuFromDto($event->getMenuDto());
    }

    /**
     * @param MenuEvent $event
     *
     * @throws HydratorException
     */
    public function onMenuUpdate(MenuEvent $event)
    {
        $this->menuService->updateMenuFromDto($event->getMenuDto(), $event->getMenu());
    }

    /**
     * @param MenuEvent $event
     *
     * @throws ReflectionException
     */
    public function onMenuDelete(MenuEvent $event)
    {
        $this->menuService->deleteMenu($event->getMenu());
    }

    /**
     * @param MenuEvent $event
     *
     * @throws ReflectionException
     */
    public function onMenuSoftDelete(MenuEvent $event)
    {
        $this->menuService->deleteSoftMenu($event->getMenu());
    }
}
