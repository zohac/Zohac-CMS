<?php

namespace App\Controller;

use App\Dto\user\UserDto;
use App\Entity\User;
use App\Event\User\UserCreateEvent;
use App\Event\User\UserCreateViewEvent;
use App\Event\User\UserPreCreateEvent;
use App\Form\UserCreateType;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends DefaultController
{
    /**
     * @Route("/users", name="users.list")
     *
     * @param UserRepository $userRepository
     * @param UserService $userService
     *
     * @return Response
     */
    public function list(UserRepository $userRepository, UserService $userService): Response
    {
//        $users = $userRepository->findAll();
        $users = $userRepository->findAllNotArchived();

        $this->getViewService()->setData('user/index.html.twig', ['users' => $users]);

        $userService->dispatchEvent(UserCreateViewEvent::NAME, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @Route("/users/{uuid}", name="users.detail")
     *
     * @param User $user
     *
     * @return Response
     */
    public function detail(?User $user = null): Response
    {
        $this->getViewService()->setData('user/detail.html.twig', ['user' => $user]);

        return $this->getResponse();
    }

    /**
     * @Route("/users/create", name="users.create")
     *
     * @param Request $request
     * @param UserService $userService
     * @param UserDto $userDto
     *
     * @return Response
     */
    public function create(Request $request, UserService $userService, UserDto $userDto): Response
    {
        $form = $this->createForm(UserCreateType::class, $userDto);

        $userService->dispatchEvent(UserPreCreateEvent::NAME, [
            'form' => $form,
            'userDto' => $userDto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->dispatchEvent(UserCreateEvent::NAME, ['userDto' => $userDto]);

            $this->addFlashMessage('sucess', 'User', 'Utilisateur créé avec succès.');

            $this->redirectToRoute('users.list');
        }

        $this->getViewService()->setData('user/type.html.twig', ['form' => $form->createView()]);

        $userService->dispatchEvent(UserCreateViewEvent::NAME, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }
}
