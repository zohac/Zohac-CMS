<?php

namespace App\Controller;

use App\Event\IndexViewEvent;
use App\Form\DeleteType;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EventService;
use App\Service\FlashBagService;
use App\Service\TranslatorService;
use App\Service\ViewService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    /**
     * @var ViewService
     */
    private $viewService;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var TranslatorService
     */
    private $translatorService;

    /**
     * DefaultController constructor.
     *
     * @param ViewService       $viewService
     * @param FlashBagInterface $flashBag
     * @param EventService      $eventService
     * @param TranslatorService $translatorService
     */
    public function __construct(
        ViewService $viewService,
        FlashBagInterface $flashBag,
        EventService $eventService,
        TranslatorService $translatorService
    ) {
        $this->viewService = $viewService;
        $this->flashBag = $flashBag;
        $this->eventService = $eventService;
        $this->translatorService = $translatorService;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $this->getViewService()->setData('base.html.twig');

        $this->dispatchEvent(IndexViewEvent::INDEX, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @return ViewService
     */
    public function getViewService(): ViewService
    {
        return $this->viewService;
    }

    /**
     * @param string     $eventName
     * @param array|null $data
     */
    public function dispatchEvent(string $eventName, ?array $data = [])
    {
        $this->eventService->dispatchEvent($eventName, $data);
    }

    /**
     * @param string|null   $view
     * @param array         $options
     * @param Response|null $response
     *
     * @return Response
     */
    public function getResponse(?string $view = null, array $options = [], Response $response = null): Response
    {
        if (null === $view && null === $this->viewService->getView()) {
            return new Response();
        }

        if (null === $view && null !== $this->viewService->getView()) {
            $view = $this->viewService->getView();
            $options = $this->viewService->getOptions();
        }

        return $this->render($view, $options, $response);
    }

    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param ServiceInterface                 $service
     *
     * @return Response
     */
    public function list(ServiceEntityRepositoryInterface $repository, ServiceInterface $service): Response
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
     * @param object           $entity
     * @param ServiceInterface $service
     *
     * @return Response
     */
    public function detail(object $entity, ServiceInterface $service): Response
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
     * @param ServiceInterface $service
     *
     * @return Response
     */
    public function create(Request $request, ServiceInterface $service): Response
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
     * @param ServiceInterface $service
     *
     * @return Response
     */
    public function redirectToList(ServiceInterface $service): Response
    {
        return $this->redirectToRoute($service->getEntityNamePlural().'.list');
    }

    /**
     * @param Request          $request
     * @param ServiceInterface $service
     *
     * @return Response
     */
    public function update(Request $request, ServiceInterface $service): Response
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
     * @param ServiceInterface $service
     *
     * @return Response
     */
    public function delete(Request $request, ServiceInterface $service): Response
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

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     * @param string|null $domaine
     *
     * @return $this
     */
    public function addAndTransFlashMessage(
        string $type = FlashBagService::FLASH_SUCCESS,
        ?string $title = null,
        ?string $content = null,
        ?string $domaine = null
    ): self {
        $title = $this->trans($title, $domaine);
        $content = $this->trans($content, $domaine);

        $this->addFlashMessage($type, $title, $content);

        return $this;
    }

    /**
     * @param string      $string
     * @param string|null $domain
     * @param array       $args
     * @param string|null $locale
     *
     * @return string
     */
    public function trans(string $string, string $domain = null, array $args = [], ?string $locale = null): string
    {
        return $this->translatorService->trans($string, $domain, $args, $locale);
    }

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     *
     * @return $this
     */
    public function addFlashMessage(
        string $type = FlashBagService::FLASH_SUCCESS,
        ?string $title = null,
        ?string $content = null
    ): self {
        $message = [
            'title' => $title,
            'message' => $content,
        ];
        $this->flashBag->add($type, $message);

        return $this;
    }
}
