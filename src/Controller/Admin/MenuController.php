<?php

namespace App\Controller\Admin;

use App\Dto\Menu\MenuDto;
use App\Entity\Menu;
use App\Exception\DtoHandlerException;
use App\Exception\HydratorException;
use App\Form\MenuType;
use App\Interfaces\ControllerInterface;
use App\Repository\MenuRepository;
use App\Service\FlashBagService;
use App\Service\Menu\MenuService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MenuController.
 *
 * @Route("/admin/menu")
 * @IsGranted("ROLE_ADMIN")
 */
class MenuController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    const TEMPLATE = '@admin';

    /**
     * @Route("/", name="menu.list", methods={"GET"})
     *
     * @param MenuRepository $menuRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function menuIndex(MenuRepository $menuRepository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($menuRepository, Menu::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="menu.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     *
     * @param Menu|null $menu
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function menuShow(?Menu $menu = null): Response
    {
        if (!$menu) {
            return $this->menuNotFound();
        }

        return $this->show($menu);
    }

    /**
     * @return Response
     */
    public function menuNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'Menu',
            'The menu was not found.',
            'menu'
        );

        return $this->redirectToRoute('menu.list');
    }

    /**
     * @Route("/create/", name="menu.create", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param MenuDto $menuDto
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function menuNew(Request $request, MenuDto $menuDto): Response
    {
        return $this->new($request, $menuDto, Menu::class, MenuType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="menu.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request   $request
     * @param Menu|null $menu
     *
     * @return Response
     *
     * @throws HydratorException
     * @throws ReflectionException
     * @throws DtoHandlerException
     */
    public function menuEdit(Request $request, ?Menu $menu = null): Response
    {
        if (!$menu) {
            return $this->menuNotFound();
        }

        return $this->edit($request, $menu, MenuType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/delete/",
     *     name="menu.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request     $request
     * @param MenuService $service
     * @param Menu|null   $menu
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function menuDelete(Request $request, MenuService $service, ?Menu $menu = null): Response
    {
        if (!$menu) {
            return $this->menuNotFound();
        }

        return $this->delete($request, $menu, $service);
    }
}
