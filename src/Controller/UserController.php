<?php

namespace App\Controller;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\FlashBagService;
use App\Service\User\UserService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/users", name="users.list")
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function userList(UserRepository $userRepository): Response
    {
        return $this->list($userRepository, User::class);
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
     *
     * @throws ReflectionException
     */
    public function userDetail(?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->detail($user);
    }

    /**
     * @return Response
     */
    public function userNotFound(): Response
    {
        $this->addAndTransFlashMessage(
            FlashBagService::FLASH_ERROR,
            'User',
            'The user was not found.',
            'user'
        );

        return $this->redirectToRoute('users.list');
    }

    /**
     * @Route("/users/create", name="users.create")
     *
     * @param Request $request
     * @param UserDto $userDto
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function userCreate(Request $request, UserDto $userDto): Response
    {
        return $this->create($request, $userDto, User::class, UserType::class);
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
     * @param User|null   $user
     *
     * @return Response
     *
     * @throws ReflectionException
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

        return $this->update($request, $userService);
    }

    /**
     * @Route(
     *     "/users/{uuid}/delete",
     *     name="users.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"}
     * )
     *
     * @param Request     $request
     * @param UserService $userService
     * @param User|null   $user
     *
     * @return Response
     */
    public function userDelete(Request $request, UserService $userService, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        $userService->setEntity($user);

        return $this->delete($request, $userService);
    }
}
