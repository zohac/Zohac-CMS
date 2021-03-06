<?php

namespace App\Controller\Admin;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Exception\DtoHandlerException;
use App\Exception\EventException;
use App\Exception\HydratorException;
use App\Form\UserType;
use App\Interfaces\ControllerInterface;
use App\Repository\UserRepository;
use App\Service\FlashBagService;
use App\Service\User\UserService;
use App\Traits\ControllerTrait;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/admin/user")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController implements ControllerInterface
{
    use ControllerTrait;

    const TEMPLATE = '@admin';

    /**
     * @Route("/", name="user.list", methods={"GET"})
     *
     * @param UserRepository $userRepository
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function userIndex(UserRepository $userRepository): Response
    {
        $repositoryOptions = [];

        // TODO: if $soft, $repositoryOptions = ['archived' => false];

        return $this->index($userRepository, User::class, $repositoryOptions);
    }

    /**
     * @Route(
     *     "/{uuid}/",
     *     name="user.detail",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET"}
     * )
     * @Entity("user", expr="repository.findOneByUuid(uuid)")
     *
     * @param User|null $user
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
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
     * @Route("/create/", name="user.create", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param UserDto $userDto
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function userNew(Request $request, UserDto $userDto): Response
    {
        return $this->new($request, $userDto, User::class, UserType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/update/",
     *     name="user.update",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     * @Entity("user", expr="repository.findOneByUuid(uuid)")
     *
     * @param Request   $request
     * @param User|null $user
     *
     * @return Response
     *
     * @throws HydratorException
     * @throws ReflectionException
     * @throws DtoHandlerException
     * @throws EventException
     */
    public function userEdit(Request $request, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->edit($request, $user, UserType::class);
    }

    /**
     * @Route(
     *     "/{uuid}/delete/",
     *     name="user.delete",
     *     requirements={"uuid"="[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request     $request
     * @param UserService $service
     * @param User|null   $user
     *
     * @return Response
     *
     * @throws ReflectionException
     * @throws EventException
     */
    public function userDelete(Request $request, UserService $service, ?User $user = null): Response
    {
        if (!$user) {
            return $this->userNotFound();
        }

        return $this->delete($request, $user, $service);
    }
}
