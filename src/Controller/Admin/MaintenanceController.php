<?php

namespace App\Controller\Admin;

use App\Entity\Maintenance;
use App\Exception\DtoHandlerException;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Form\MaintenanceType;
use App\Interfaces\ControllerInterface;
use App\Repository\MaintenanceRepository;
use App\Service\FlashBagService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MaintenanceController.
 *
 * @Route("/admin/maintenance")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class MaintenanceController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    const TEMPLATE = '@admin';

    /**
     * @Route("/", name="maintenance.list", methods={"GET"})
     *
     * @param MaintenanceRepository $repository
     *
     * @return Response
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function maintenanceIndex(MaintenanceRepository $repository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($repository, Maintenance::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="maintenance.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     *
     * @param Maintenance|null $maintenance
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function maintenanceShow(?Maintenance $maintenance = null): Response
    {
        if (!$maintenance) {
            return $this->maintenanceNotFound();
        }

        return $this->show($maintenance);
    }

    /**
     * @return Response
     */
    public function maintenanceNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'Maintenance',
            'The maintenance was not found.',
            'maintenance'
        );

        return $this->redirectToRoute('maintenance.list');
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="maintenance.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request          $request
     * @param Maintenance|null $maintenance
     *
     * @return Response
     *
     * @throws DtoHandlerException
     * @throws EventException
     * @throws HydratorException
     * @throws ReflectionException
     */
    public function maintenanceEdit(Request $request, ?Maintenance $maintenance = null): Response
    {
        if (!$maintenance) {
            return $this->maintenanceNotFound();
        }

        return $this->edit($request, $maintenance, MaintenanceType::class);
    }
}
