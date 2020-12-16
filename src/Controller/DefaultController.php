<?php

namespace App\Controller;

use App\Event\IndexViewEvent;
use App\Exception\EventException;
use App\Interfaces\ControllerInterface;
use App\Traits\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     *
     * @throws EventException
     */
    public function defaultIndex(): Response
    {
        $this->getViewService()->setData('home/home.html.twig');

        $this->dispatchEvent(IndexViewEvent::INDEX, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }
}
