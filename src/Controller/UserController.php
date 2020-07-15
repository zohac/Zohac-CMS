<?php

namespace App\Controller;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Form\UserType;
use App\Interfaces\ControllerInterface;
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
 *
 * @Route("/user")
 */
class UserController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    /**
     * @Route("/", name="user.list", methods="GET")
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function userIndex(UserRepository $userRepository): Response
    {
        return $this->index($userRepository, User::class);
    }

    /**
     * @Route(
     *     "/{uuid}",
     *     name="user.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods="GET"
     * )
     *
     * @param User|null $user
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function userShow(?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->show($user);
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

        return $this->redirectToRoute('user.list');
    }

    /**
     * @Route("/create", name="user.create")
     *
     * @param Request $request
     * @param UserDto $userDto
     *
     * @return Response
     *
     * @throws ReflectionException
     */
    public function userNew(Request $request, UserDto $userDto): Response
    {
        return $this->new($request, $userDto, User::class, UserType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update",
     *     name="user.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods="GET, POST"
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
    public function userEdit(Request $request, UserService $userService, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        $userDto = $userService->createUserDtoFromUser($user);

        $userService
            ->setFormType(UserType::class)
            ->setDto($userDto)
            ->setEntity($user);

        return $this->edit($request, $userService);
    }

    /**
     * @Route(
     *     "/{uuid}/delete",
     *     name="user.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods="GET, POST"
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
