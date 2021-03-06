<?php

namespace App\Traits;

use App\Exception\DtoHandlerException;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Form\DeleteType;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\FlashBagService;
use App\Service\TranslatorService;
use App\Service\ViewService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Trait ControllerTrait.
 */
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
     * @param string                           $entity            the class name (Entity::class)
     * @param array|null                       $repositoryOptions
     *
     * @return Response
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function index(
        ServiceEntityRepositoryInterface $repository,
        string $entity,
        ?array $repositoryOptions = []
    ): Response {
        $this->entityService->setEntity($entity);

        $entities = $repository->findAllInOneRequest($repositoryOptions);

        return $this->viewIndex($entities);
    }

    public function getTemplatePath(): ?string
    {
        return defined('self::TEMPLATE') ? self::TEMPLATE.'/' : '';
    }

    /**
     * @param array $entities
     *
     * @return Response
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function viewIndex(array $entities): Response
    {
        $path = $this->getTemplatePath().$this->entityService->getEntityNameToLower().'/index.html.twig';

        $this->getViewService()->setData($path, [
            $this->entityService->getEntityNamePlural() => $entities,
        ]);

        $this->dispatchEvent($this->getViewEvent('LIST'), [ViewService::NAME => $this->getViewService()]);

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
     *
     * @throws EventException
     */
    public function dispatchEvent(string $eventName, ?array $data = [])
    {
        $this->entityService->getEventService()->dispatchEvent($eventName, $data);
    }

    /**
     * @param string $eventName
     *
     * @return ViewEventInterface
     *
     * @throws ReflectionException
     */
    public function getViewEvent(string $eventName): string
    {
        return $this->entityService->getViewEvent($eventName);
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
     * @param EntityInterface|null $entity
     *
     * @return Response
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function show(?EntityInterface $entity = null): Response
    {
        $this->entityService->setEntity($entity);

        $path = $this->getTemplatePath().$this->entityService->getEntityNameToLower().'/detail.html.twig';

        $this->getViewService()->setData($path, [
            $this->entityService->getEntityNameToLower() => $entity,
        ]);

        $this->dispatchEvent($this->getViewEvent('DETAIL'), [ViewService::NAME => $this->getViewService()]);

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
     * @throws EventException
     * @throws ReflectionException
     */
    public function new(Request $request, DtoInterface $dto, string $entity, string $formType): Response
    {
        $this->entityService->setEntity($entity);

        $form = $this->createForm($formType, $dto, [
            'action' => $this->generateUrl(strtolower($this->entityService->getEntityShortName()).'.create'),
        ]);

        $this->dispatchEvent($this->getEvent('PRE_CREATE'), [
            'form' => $form,
            $this->entityService->getEntityShortName().'Dto' => $dto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($this->getEvent('CREATE'), [
                $this->entityService->getEntityShortName().'Dto' => $dto,
            ]);

            return $this->redirectToList();
        }

        $path = $this->getTemplatePath().$this->entityService->getEntityNameToLower().'/type.html.twig';

        $this->getViewService()->setData($path, [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($this->getViewEvent('CREATE'), [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param string $eventName
     *
     * @return string
     *
     * @throws ReflectionException
     */
    public function getEvent(string $eventName): string
    {
        return $this->entityService->getEvent($eventName);
    }

    /**
     * @return Response
     */
    public function redirectToList(): Response
    {
        return $this->redirectToRoute(strtolower($this->entityService->getEntityShortName()).'.list');
    }

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     * @param string          $formType
     *
     * @return Response
     *
     * @throws DtoHandlerException
     * @throws EventException
     * @throws HydratorException
     * @throws ReflectionException
     */
    public function edit(Request $request, EntityInterface $entity, string $formType): Response
    {
        $this->entityService->setEntity($entity)->getAndHydrateDto();

        $form = $this->createForm($formType, $this->entityService->getDto(), [
            'action' => $this->generateUrl(strtolower($this->entityService->getEntityShortName()).'.update', [
                'uuid' => $this->entityService->getEntity()->getUuid(),
            ]),
        ]);

        $this->dispatchEvent($this->getEvent('PRE_UPDATE'), [
            'form' => $form,
            $this->entityService->getEntityShortName().'Dto' => $this->entityService->getDto(),
            $this->entityService->getEntityShortName() => $this->entityService->getEntity(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent($this->getEvent('UPDATE'), [
                $this->entityService->getEntityShortName().'Dto' => $this->entityService->getDto(),
                $this->entityService->getEntityShortName() => $this->entityService->getEntity(),
            ]);

            return $this->redirectToList();
        }

        $path = $this->getTemplatePath().$this->entityService->getEntityNameToLower().'/type.html.twig';

        $this->getViewService()->setData($path, [
            'form' => $form->createView(),
        ]);

        $this->dispatchEvent($this->getViewEvent('UPDATE'), [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @param Request          $request
     * @param EntityInterface  $entity
     * @param ServiceInterface $service
     * @param string|null      $option
     *
     * @return Response
     *
     * @throws EventException
     * @throws ReflectionException
     */
    public function delete(
        Request $request,
        EntityInterface $entity,
        ServiceInterface $service,
        ?string $option = null
    ): Response {
        $this->entityService->setEntity($entity);

        $form = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl(strtolower($this->entityService->getEntityShortName()).'.delete', [
                'uuid' => $this->entityService->getEntity()->getUuid(),
            ]),
        ]);

        $this->dispatchEvent($this->getEvent('PRE_DELETE'), [
            'form' => $form,
            $this->entityService->getEntityNameToLower() => $this->entityService->getEntity(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eventName = ('soft' === $option) ? 'SOFT_DELETE' : 'DELETE';

            $this->dispatchEvent($this->getEvent($eventName), [
                $this->entityService->getEntityNameToLower() => $this->entityService->getEntity(),
            ]);

            return $this->redirectToList();
        }

        $this->getViewService()->setData($this->getTemplatePath().'/delete.html.twig', [
            'form' => $form->createView(),
            'message' => $service->getDeleteMessage($entity),
        ]);

        $this->dispatchEvent($this->getViewEvent('DELETE'), [ViewService::NAME => $this->getViewService()]);

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
