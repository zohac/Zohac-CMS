<?php

namespace App\Controller;

use App\Event\IndexViewEvent;
use App\Service\EventService;
use App\Service\TranslatorService;
use App\Service\ViewService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    const FLASH_SUCCESS = 'success';
    const FLASH_ERROR = 'error';

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
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     * @param string|null $domaine
     *
     * @return $this
     */
    public function addAndTransFlashMessage(
        string $type = self::FLASH_SUCCESS,
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
        string $type = self::FLASH_SUCCESS,
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
     * @param string $entityName
     * @return Response
     */
    public function list(ServiceEntityRepositoryInterface $repository, string $entityName): Response
    {
        $entityName = strtolower($entityName);
        $list = $this->getViewService()->getConstant($entityName, 'list');

        $entities = $repository->findAll();

        $this->getViewService()->setData($entityName.'/index.html.twig', [$entityName.'s' => $entities]);

        $this->dispatchEvent($list, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }
}
