<?php

namespace App\Controller\Admin;

use App\Dto\Role\RoleDto;
use App\Entity\Role;
use App\Exception\DtoHandlerException;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Form\RoleType;
use App\Interfaces\ControllerInterface;
use App\Repository\RoleRepository;
use App\Service\FlashBagService;
use App\Service\Role\RoleService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoleController.
 *
 * @Route("/admin/role")
 * @IsGranted("ROLE_ADMIN")
 */
class RoleController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    /**
     * @Route("/", name="role.list", methods={"GET"})
     *
     * @param RoleRepository $roleRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function roleIndex(RoleRepository $roleRepository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($roleRepository, Role::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="role.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     * @Entity("role", expr="repository.findOneByUuid(uuid)")
     *
     * @param Role|null $role
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function roleShow(?Role $role = null): Response
    {
        if (!$role) {
            return $this->roleNotFound();
        }

        return $this->show($role);
    }

    /**
     * @return Response
     */
    public function roleNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'Role',
            'The role was not found.',
            'role'
        );

        return $this->redirectToRoute('role.list');
    }

    /**
     * @Route("/create/", name="role.create", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param RoleDto $roleDto
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function roleNew(Request $request, RoleDto $roleDto): Response
    {
        return $this->new($request, $roleDto, Role::class, RoleType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="role.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     * @Entity("role", expr="repository.findOneByUuid(uuid)")
     *
     * @param Request   $request
     * @param Role|null $role
     *
     * @return Response
     *
     * @throws HydratorException
     * @throws ReflectionException
     * @throws DtoHandlerException
     * @throws EventException
     */
    public function roleEdit(Request $request, ?Role $role = null): Response
    {
        if (!$role) {
            return $this->roleNotFound();
        }

        return $this->edit($request, $role, RoleType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/delete/",
     *     name="role.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request     $request
     * @param RoleService $service
     * @param Role|null   $role
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function roleDelete(Request $request, RoleService $service, ?Role $role = null): Response
    {
        if (!$role) {
            return $this->roleNotFound();
        }

        return $this->delete($request, $role, $service);
    }
}
