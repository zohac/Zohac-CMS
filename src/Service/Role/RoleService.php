<?php

namespace App\Service\Role;

use App\Dto\Role\RoleDto;
use App\Entity\Role;
use App\Event\Role\RoleEvent;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Repository\RoleRepository;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class RoleService implements ServiceInterface
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
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * RoleService constructor.
     *
     * @param EventService    $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService   $entityService
     * @param RoleRepository  $roleRepository
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService,
        RoleRepository $roleRepository
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param RoleDto $roleDto
     * @return Role
     * @throws EventException
     * @throws HydratorException
     */
    public function createRoleFromDto(RoleDto $roleDto): Role
    {
        /** @var Role $role */
        $role = $this->entityService->hydrateEntityWithDto(new Role(), $roleDto);

        $this->eventService->dispatchEvent(RoleEvent::POST_CREATE, [
            'role' => $role,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Role',
            'Role successfully created.',
            'role'
        );

        return $role;
    }

    /**
     * @param RoleDto $roleDto
     * @param Role $role
     * @return Role
     * @throws EventException
     * @throws HydratorException
     */
    public function updateRoleFromDto(RoleDto $roleDto, Role $role): Role
    {
        /** @var Role $role */
        $role = $this->entityService->hydrateEntityWithDto($role, $roleDto);

        $this->eventService->dispatchEvent(RoleEvent::POST_UPDATE, [
            'role' => $role,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Role',
            'Role successfully updated.',
            'role'
        );

        return $role;
    }

    /**
     * @param Role $role
     * @return $this
     * @throws EventException
     * @throws ReflectionException
     */
    public function deleteRole(Role $role): self
    {
        $this->entityService
            ->setEntity($role)
            ->remove($role)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Role',
            'Role successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(RoleEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param Role $role
     * @return $this
     * @throws ReflectionException
     * @throws EventException
     */
    public function deleteSoftRole(Role $role)
    {
        $role->setArchived(true);

        $this->entityService
            ->setEntity($role)
            ->persist($role)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Role',
            'Role successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(RoleEvent::POST_DELETE);

        return $this;
    }

    public function getRoleForForm(): array
    {
        $roles = $this->roleRepository->findRolesForForm(['archived' => false]);

        $rolesForForm = [];
        foreach ($roles as $role) {
            $rolesForForm[$role['name']] = $role['uuid'];
        }

        return $rolesForForm;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getDeleteMessage(EntityInterface $entity): string
    {
        /* @var Role $entity */
        return $this->flashBagService->trans(
            'Are you sure you want to delete this role (%role%) ?',
            'role',
            ['role' => $entity->getName()]
        );
    }
}
