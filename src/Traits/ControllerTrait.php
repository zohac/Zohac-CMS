<?php

namespace App\Traits;

use App\Entity\User;
use App\Form\DeleteType;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityServiceInterface;
use App\Service\EntityService;
use App\Service\FlashBagService;
use App\Service\TranslatorService;
use App\Service\ViewService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

trait ControllerTrait
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
     * @var TranslatorService
     */
    private $translatorService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * ControllerTrait constructor.
     *
     * @param ViewService       $viewService
     * @param FlashBagInterface $flashBag
     * @param EntityService     $entityService
     * @param TranslatorService $translatorService
     */
    public function __construct(
        ViewService $viewService,
        FlashBagInterface $flashBag,
        EntityService $entityService,
        TranslatorService $translatorService
    ) {
        $this->viewService = $viewService;
        $this->flashBag = $flashBag;
        $this->entityService = $entityService;
        $this->translatorService = $translatorService;
    }

    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param string                           $entity
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function list(ServiceEntityRepositoryInterface $repository, string $entity): Response
    {
        $this->entityService->setEntity(User::class);

        $entities = $repository->findAll();

        $this->getViewService()->setData($this->entityService->getEntityNameToLower().'/index.html.twig', [
            $this->entityService->getEntityNamePlural() => $entities,
        ]);

        $this->dispatchEvent($this->entityService->getViewEvent('LIST'), [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param EntityInterface|null $entity
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function detail(?EntityInterface $entity = null): Response
    {
        $this->entityService->setEntity($entity);

        $this->getViewService()->setData($this->entityService->getEntityNameToLower().'/detail.html.twig', [
            $this->entityService->getEntityNameToLower() => $entity,
        ]);

        $this->dispatchEvent($this->entityService->getViewEvent('DETAIL'), [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request      $request
     * @param DtoInterface $dto
     * @param string       $entity
     * @param string       $formType
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function create(Request $request, DtoInterface $dto, string $entity, string $formType): Response
    {
        $this->entityService
            ->setDto($dto)
            ->setEntity($entity)
            ->setFormType($formType);

        $form = $this->createForm($formType, $dto, [
            'action' => $this->generateUrl($this->entityService->getEntityNamePlural().'.create'),
        ]);

        $this->dispatchEvent($this->entityService->getEvent('PRE_CREATE'), [
            'form' => $form,
            $this->entityService->getEntityShortName().'Dto' => $dto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($this->entityService->getEvent('CREATE'), [
                $this->entityService->getEntityShortName().'Dto' => $dto,
            ]);

            return $this->redirectToList();
        }

        $this->getViewService()->setData($this->entityService->getEntityNameToLower().'/type.html.twig', [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($this->entityService->getViewEvent('CREATE'), [
            ViewService::NAME => $this->getViewService(),
        ]);

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

            return $this->redirectToList();
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

            return $this->redirectToList();
        }

        $this->getViewService()->setData('delete.html.twig', [
            'form' => $form->createView(),
            'message' => $service->getDeleteMessage(),
        ]);

        $this->dispatchEvent($service->getViewEvent()::DELETE, [ViewService::NAME => $this->getViewService()]);

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
        $this->entityService->getEventService()->dispatchEvent($eventName, $data);
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
     * @return Response
     */
    public function redirectToList(): Response
    {
        return $this->redirectToRoute($this->entityService->getEntityNamePlural().'.list');
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
