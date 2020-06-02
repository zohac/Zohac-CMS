<?php

namespace App\Controller;

use App\Dto\user\UserDto;
use App\Entity\User;
use App\Event\User\UserCreateEvent;
use App\Event\User\UserCreateViewEvent;
use App\Event\User\UserPreCreateEvent;
use App\Event\User\UserPreUpdateEvent;
use App\Event\User\UserUpdateEvent;
use App\Event\User\UserUpdateViewEvent;
use App\Form\UserType;
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
     * @param UserService    $userService
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
     * @Route(
     *     "/users/{uuid}",
     *     name="users.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
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
     * @param Request     $request
     * @param UserService $userService
     * @param UserDto     $userDto
     *
     * @return Response
     */
    public function create(Request $request, UserService $userService, UserDto $userDto): Response
    {
        $form = $this->createForm(UserType::class, $userDto);

        $userService->dispatchEvent(UserPreCreateEvent::NAME, [
            'form' => $form,
            'userDto' => $userDto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->dispatchEvent(UserCreateEvent::NAME, ['userDto' => $userDto]);

            $this->addFlashMessage('sucess', 'User', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('users.list');
        }

        $this->getViewService()->setData('user/type.html.twig', ['form' => $form->createView()]);

        $userService->dispatchEvent(UserCreateViewEvent::NAME, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/users/{uuid}/update",
     *     name="users.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param Request     $request
     * @param UserService $userService
     * @param User        $user
     *
     * @return Response
     */
    public function update(Request $request, UserService $userService, User $user): Response
    {
        $userDto = $userService->createUserDtoFromUser($user);
        $form = $this->createForm(UserType::class, $userDto);

        $userService->dispatchEvent(UserPreUpdateEvent::NAME, [
            'form' => $form,
            'userDto' => $userDto,
            'user' => $user,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService->dispatchEvent(UserUpdateEvent::NAME, [
                'userDto' => $userDto,
                'user' => $user,
            ]);

            $this->addFlashMessage('sucess', 'User', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('users.list');
        }

        $this->getViewService()->setData('user/type.html.twig', ['form' => $form->createView()]);

        $userService->dispatchEvent(UserUpdateViewEvent::NAME, ['viewService' => $this->getViewService()]);

        return $this->getResponse();
    }
}
