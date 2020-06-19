<?php

namespace App\Controller;

use App\Event\IndexViewEvent;
use App\Service\DefaultService;
use App\Service\ViewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @var DefaultService
     */
    private $defaultService;

    /**
     * DefaultController constructor.
     *
     * @param ViewService       $viewService
     * @param FlashBagInterface $flashBag
     * @param DefaultService    $defaultService
     */
    public function __construct(
        ViewService $viewService,
        FlashBagInterface $flashBag,
        DefaultService $defaultService
    ) {
        $this->viewService = $viewService;
        $this->flashBag = $flashBag;
        $this->defaultService = $defaultService;
    }

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     *
     * @return $this
     */
    public function addFlashMessage(string $type = 'success', ?string $title = null, ?string $content = null): self
    {
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
        $this->defaultService->dispatchEvent($eventName, $data);
    }

    /**
     * @param string        $view
     * @param array         $options
     * @param Response|null $response
     *
     * @return Response
     */
    public function getResponse(string $view = null, array $options = [], Response $response = null): Response
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
}
