<?php

namespace App\Controller;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\Form\DeleteType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Service\ViewService;
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
     *
     * @return Response
     */
    public function userList(UserRepository $userRepository): Response
    {
        return $this->list($userRepository, 'user');
    }

    /**
     * @Route(
     *     "/users/{uuid}",
     *     name="users.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param User|null $user
     *
     * @return Response
     */
    public function userDetail(?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->detail($user, 'user');
    }

    /**
     * @return Response
     */
    public function userNotFound(): Response
    {
        $this->addAndTransFlashMessage(self::FLASH_ERROR, 'User', 'The user was not found.', 'user');

        return $this->redirectToUserList();
    }

    /**
     * @return Response
     */
    public function redirectToUserList(): Response
    {
        return $this->redirectToRoute('users.list');
    }

    /**
     * @Route("/users/create", name="users.create")
     *
     * @param Request $request
     * @param UserDto $userDto
     *
     * @return Response
     */
    public function create(Request $request, UserDto $userDto): Response
    {
        $form = $this->createForm(UserType::class, $userDto, [
            'action' => $this->generateUrl('users.create'),
        ]);

        $this->dispatchEvent(UserEvent::PRE_CREATE, [
            'form' => $form,
            'userDto' => $userDto,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(UserEvent::CREATE, ['userDto' => $userDto]);

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'User', 'User successfully created.', 'user');

            return $this->redirectToUserList();
        }

        $this->getViewService()->setData('user/type.html.twig', ['form' => $form->createView()]);

        $this->dispatchEvent(UserViewEvent::CREATE, [ViewService::NAME => $this->getViewService()]);

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
    public function update(Request $request, UserService $userService, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        $userDto = $userService->createUserDtoFromUser($user);
        $form = $this->createForm(UserType::class, $userDto, [
            'action' => $this->generateUrl('users.update', ['uuid' => $user->getUuid()]),
        ]);

        $this->dispatchEvent(UserEvent::PRE_UPDATE, [
            'form' => $form,
            'userDto' => $userDto,
            'user' => $user,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(UserEvent::UPDATE, [
                'userDto' => $userDto,
                'user' => $user,
            ]);

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'User', 'User successfully updated.', 'user');

            return $this->redirectToUserList();
        }

        $this->getViewService()->setData('user/type.html.twig', ['form' => $form->createView()]);

        $this->dispatchEvent(UserViewEvent::UPDATE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }

    /**
     * @Route(
     *     "/users/{uuid}/delete",
     *     name="users.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param Request   $request
     * @param User|null $user
     *
     * @return Response
     */
    public function delete(Request $request, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        $form = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('users.delete', ['uuid' => $user->getUuid()]),
        ]);

        $this->dispatchEvent(UserEvent::PRE_DELETE, [
            'form' => $form,
            'user' => $user,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatchEvent(UserEvent::DELETE, ['user' => $user]);

            $this->addAndTransFlashMessage(self::FLASH_SUCCESS, 'User', 'User successfully deleted.', 'user');

            return $this->redirectToUserList();
        }

        $this->getViewService()->setData('delete.html.twig', [
            'form' => $form->createView(),
            'message' => $this->trans('Are you sure you want to delete this user (%email%) ?', 'user', [
                'email' => $user->getEmail(),
            ]),
        ]);

        $this->dispatchEvent(UserViewEvent::DELETE, [ViewService::NAME => $this->getViewService()]);

        return $this->getResponse();
    }
}
