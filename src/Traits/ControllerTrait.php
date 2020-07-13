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
     * @param EntityServiceInterface           $service
     *
     * @return Response
     */
    public function list(ServiceEntityRepositoryInterface $repository, EntityServiceInterface $service): Response
    {
        $entities = $repository->findAll();

        $this->getViewService()->setData($service->getEntityNameToLower().'/index.html.twig', [
            $service->getEntityNamePlural() => $entities,
        ]);

        $this->dispatchEvent($service->getViewEvent()::LIST, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param EntityServiceInterface $service
     * @param EntityInterface|null   $entity
     *
     * @return Response
     */
    public function detail(EntityServiceInterface $service, ?EntityInterface $entity = null): Response
    {
        $this->getViewService()->setData($service->getEntityNameToLower().'/detail.html.twig', [
            $service->getEntityNameToLower() => $entity,
        ]);

        $this->dispatchEvent($service->getViewEvent()::DETAIL, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request                $request
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
            $service->getEntityShortName().'Dto' => $service->getDto(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($service->getEvent()::CREATE, [
                $service->getEntityShortName().'Dto' => $service->getDto(),
            ]);

            return $this->redirectToList($service);
        }

        $this->getViewService()->setData($service->getEntityNameToLower().'/type.html.twig', [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($service->getViewEvent()::CREATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request                $request
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
            $service->getEntityShortName().'Dto' => $service->getDto(),
            $service->getEntityShortName() => $service->getEntity(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($service->getEvent()::UPDATE, [
                $service->getEntityShortName().'Dto' => $service->getDto(),
                $service->getEntityShortName() => $service->getEntity(),
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
     * @param Request                $request
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
