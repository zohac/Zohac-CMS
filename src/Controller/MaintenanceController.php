<?php

namespace App\Controller;

use App\Dto\Maintenance\MaintenanceDto;
use App\Entity\Maintenance;
use App\Exception\DtoHandlerException;
use App\Exception\HydratorException;
use App\Form\MaintenanceType;
use App\Interfaces\ControllerInterface;
use App\Repository\MaintenanceRepository;
use App\Service\FlashBagService;
use App\Service\Maintenance\MaintenanceService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MaintenanceController.
 *
 * @Route("/maintenance")
 */
class MaintenanceController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    /**
     * @Route("/", name="maintenance.list", methods={"GET"})
     *
     * @param MaintenanceRepository $maintenanceRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function maintenanceIndex(MaintenanceRepository $maintenanceRepository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($maintenanceRepository, Maintenance::class, $repositoryOptions);
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
    * @Route("/create/", name="maintenance.create", methods={"GET", "POST"})
    *
    * @param Request $request
    * @param MaintenanceDto $maintenanceDto
    *
    * @return Response
    *
    * @throws ReflectionException
    */
    public function maintenanceNew(Request $request, MaintenanceDto $maintenanceDto): Response
    {
        return $this->new($request, $maintenanceDto, Maintenance::class, MaintenanceType::class);
    }

    /**
    * @Route(
    *     "/{uuid}/update/",
    *     name="maintenance.update",
    *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
    *     methods={"GET", "POST"}
    * )
    *
    * @param Request   $request
    * @param Maintenance|null $maintenance
    *
    * @return Response
    *
    * @throws HydratorException
    * @throws ReflectionException
    * @throws DtoHandlerException
    */
    public function maintenanceEdit(Request $request, ?Maintenance $maintenance = null): Response
    {
        if (!$maintenance) {
            return $this->maintenanceNotFound();
        }

        return $this->edit($request, $maintenance, MaintenanceType::class);
    }

    /**
    * @Route(
    *     "/{uuid}/delete/",
    *     name="maintenance.delete",
    *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
    *     methods={"GET", "POST"}
    * )
    *
    * @param Request     $request
    * @param MaintenanceService $service
    * @param Maintenance|null   $maintenance
    *
    * @return Response
    *
    * @throws ReflectionException
    */
    public function maintenanceDelete(Request $request, MaintenanceService $service, ?Maintenance $maintenance = null): Response
    {
        if (!$maintenance) {
            return $this->maintenanceNotFound();
        }

        return $this->delete($request, $maintenance, $service);
    }
}
