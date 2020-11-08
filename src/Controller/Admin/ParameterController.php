<?php

namespace App\Controller\Admin;

use App\Dto\Parameter\ParameterDto;
use App\Entity\Parameter;
use App\Exception\DtoHandlerException;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Form\ParameterType;
use App\Interfaces\ControllerInterface;
use App\Repository\ParameterRepository;
use App\Service\FlashBagService;
use App\Service\Parameter\ParameterService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParameterController.
 *
 * @Route("/admin/parameter")
 * @IsGranted("ROLE_ADMIN")
 */
class ParameterController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    const TEMPLATE = '@admin';

    /**
     * @Route("/", name="parameter.list", methods={"GET"})
     *
     * @param ParameterRepository $parameterRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function parameterIndex(ParameterRepository $parameterRepository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($parameterRepository, Parameter::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="parameter.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     *
     * @param Parameter|null $parameter
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function parameterShow(?Parameter $parameter = null): Response
    {
        if (!$parameter) {
            return $this->parameterNotFound();
        }

        return $this->show($parameter);
    }

    /**
     * @return Response
     */
    public function parameterNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'Parameter',
            'The parameter was not found.',
            'parameter'
        );

        return $this->redirectToRoute('parameter.list');
    }

    /**
     * @Route("/create/", name="parameter.create", methods={"GET", "POST"})
     *
     * @param Request      $request
     * @param ParameterDto $parameterDto
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function parameterNew(Request $request, ParameterDto $parameterDto): Response
    {
        return $this->new($request, $parameterDto, Parameter::class, ParameterType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="parameter.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request        $request
     * @param Parameter|null $parameter
     *
     * @return Response
     *
     * @throws HydratorException
     * @throws ReflectionException
     * @throws DtoHandlerException
     * @throws EventException
     */
    public function parameterEdit(Request $request, ?Parameter $parameter = null): Response
    {
        if (!$parameter) {
            return $this->parameterNotFound();
        }

        return $this->edit($request, $parameter, ParameterType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/delete/",
     *     name="parameter.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request          $request
     * @param ParameterService $service
     * @param Parameter|null   $parameter
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function parameterDelete(Request $request, ParameterService $service, ?Parameter $parameter = null): Response
    {
        if (!$parameter) {
            return $this->parameterNotFound();
        }

        return $this->delete($request, $parameter, $service);
    }
}
