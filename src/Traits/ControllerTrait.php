<?php

namespace App\Traits;

use App\Form\DeleteType;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityServiceInterface;
use App\Service\ViewService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param EntityServiceInterface                 $service
     *
     * @return Response
     */
    public function list(ServiceEntityRepositoryInterface $repository, EntityServiceInterface $service): Response
    {
        $list = $this->getViewService()->getListConstant($service->getEntityNameToLower());

        $entities = $repository->findAll();

        $this->getViewService()->setData($service->getEntityNameToLower().'/index.html.twig', [
            $service->getEntityNamePlural() => $entities,
        ]);

        $this->dispatchEvent($list, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param EntityServiceInterface $service
     * @param EntityInterface $entity
     * @return Response
     */
    public function detail(EntityServiceInterface $service, ?EntityInterface $entity = null): Response
    {
        $detail = $this->getViewService()->getDetailConstant($service->getEntityNameToLower());

        $this->getViewService()->setData($service->getEntityNameToLower().'/detail.html.twig', [
            $service->getEntityNameToLower() => $entity,
        ]);

        $this->dispatchEvent($detail, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request          $request
     * @param EntityServiceInterface $service
     *
     * @return Response
     */
    public function create(Request $request, EntityServiceInterface $service): Response
    {
        $form = $this->createForm($service->getFormType(), $service->getDto(), [
            'action' => $this->generateUrl($service->getEntityNamePlural().'.create'),
        ]);

        $this->dispatchEvent($service->getEvent()::PRE_CREATE, [
            'form' => $form,
            $service->getEntityName().'Dto' => $service->getDto(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($service->getEvent()::CREATE, [$service->getEntityName().'Dto' => $service->getDto()]);

            return $this->redirectToList($service);
        }

        $this->getViewService()->setData($service->getEntityNameToLower().'/type.html.twig', [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($service->getViewEvent()::CREATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request          $request
     * @param EntityServiceInterface $service
     *
     * @return Response
     */
    public function update(Request $request, EntityServiceInterface $service): Response
    {
        $form = $this->createForm($service->getFormType(), $service->getDto(), [
            'action' => $this->generateUrl($service->getEntityNamePlural().'.update', [
                'uuid' => $service->getEntity()->getUuid(),
            ]),
        ]);

        $this->dispatchEvent($service->getEvent()::PRE_UPDATE, [
            'form' => $form,
            $service->getEntityName().'Dto' => $service->getDto(),
            $service->getEntityName() => $service->getEntity(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($service->getEvent()::UPDATE, [
                $service->getEntityName().'Dto' => $service->getDto(),
                $service->getEntityName() => $service->getEntity(),
            ]);

            return $this->redirectToList($service);
        }

        $this->getViewService()->setData($service->getEntityNameToLower().'/type.html.twig', [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($service->getViewEvent()::UPDATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request          $request
     * @param EntityServiceInterface $service
     *
     * @return Response
     */
    public function delete(Request $request, EntityServiceInterface $service): Response
    {
        $form = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl($service->getEntityNamePlural().'.delete', [
                'uuid' => $service->getEntity()->getUuid(),
            ]),
        ]);

        $this->dispatchEvent($service->getEvent()::PRE_DELETE, [
            'form' => $form,
            $service->getEntityNameToLower() => $service->getEntity(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($service->getEvent()::DELETE, [
                $service->getEntityNameToLower() => $service->getEntity(),
            ]);

            return $this->redirectToList($service);
        }

        $this->getViewService()->setData('delete.html.twig', [
            'form' => $form->createView(),
            'message' => $service->getDeleteMessage(),
        ]);

        $this->dispatchEvent($service->getViewEvent()::DELETE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }
}
