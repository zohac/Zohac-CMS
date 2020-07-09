<?php

namespace App\Controller;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\Form\DeleteType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\FlashBagService;
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
     * @param UserService    $userService
     *
     * @return Response
     */
    public function userList(UserRepository $userRepository, UserService $userService): Response
    {
        return $this->list($userRepository, $userService);
    }

    /**
     * @Route(
     *     "/users/{uuid}",
     *     name="users.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param UserService $userService
     * @param User|null   $user
     *
     * @return Response
     *
     * @throws \App\Exception\EventException
     */
    public function userDetail(UserService $userService, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->detail($user, $userService);
    }

    /**
     * @return Response
     */
    public function userNotFound(): Response
    {
        $this->addAndTransFlashMessage(FlashBagService::FLASH_ERROR, 'User', 'The user was not found.', 'user');

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
     * @param Request     $request
     * @param UserDto     $userDto
     * @param UserService $userService
     *
     * @return Response
     */
    public function userCreate(Request $request, UserDto $userDto, UserService $userService): Response
    {
        $userService
            ->setFormType(UserType::class)
            ->setDto($userDto);

        return $this->create($request, $userService);
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
    public function userUpdate(Request $request, UserService $userService, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        $userDto = $userService->createUserDtoFromUser($user);

        $userService
            ->setFormType(UserType::class)
            ->setDto($userDto)
            ->setEntity($user);

        return $this->create($request, $userService);
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

            $this->addAndTransFlashMessage(
                FlashBagService::FLASH_SUCCESS,
                'User',
                'User successfully deleted.',
                'user'
            );

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
