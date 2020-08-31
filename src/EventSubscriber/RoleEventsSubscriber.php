<?php

namespace App\EventSubscriber;

use App\Event\Role\RoleEvent;
use App\Event\Role\RoleViewEvent;
use App\Exception\HydratorException;
use App\Service\Role\RoleService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoleEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getSubscribedEvents()
    {
        return [
            RoleEvent::CREATE => ['onRoleCreate', 0],
            RoleEvent::UPDATE => ['onRoleUpdate', 0],
            RoleEvent::DELETE => ['onRoleDelete', 0],
            RoleEvent::SOFT_DELETE => ['onRoleSoftDelete', 0],
            RoleViewEvent::UPDATE => ['onRoleViewEvent', 0],
        ];
    }

    /**
     * @param RoleEvent $event
     *
     * @throws HydratorException
     */
    public function onRoleCreate(RoleEvent $event)
    {
        $this->roleService->createRoleFromDto($event->getRoleDto());
    }

    /**
     * @param RoleEvent $event
     *
     * @throws HydratorException
     */
    public function onRoleUpdate(RoleEvent $event)
    {
        $this->roleService->updateRoleFromDto($event->getRoleDto(), $event->getRole());
    }

    /**
     * @param RoleEvent $event
     *
     * @throws ReflectionException
     */
    public function onRoleDelete(RoleEvent $event)
    {
        $this->roleService->deleteRole($event->getRole());
    }

    /**
     * @param RoleEvent $event
     *
     * @throws ReflectionException
     */
    public function onRoleSoftDelete(RoleEvent $event)
    {
        $this->roleService->deleteSoftRole($event->getRole());
    }

    public function onRoleViewEvent(RoleViewEvent $event)
    {
        dump($event);
    }
}
