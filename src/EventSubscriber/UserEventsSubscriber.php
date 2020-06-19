<?php

namespace App\EventSubscriber;

use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\Exception\UuidException;
use App\Service\User\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::CREATE => ['onUserCreate', 0],
            UserEvent::PRE_UPDATE => ['onUserPreUpdate', 0],
            UserEvent::UPDATE => ['onUserUpdate', 0],
            UserEvent::DELETE => ['onUserDelete', 0],
            UserViewEvent::CREATE => ['onUserCreateView', 0],
            UserViewEvent::UPDATE => ['onUserUpdateView', 0],
            UserViewEvent::DELETE => ['onUserDeleteView', 0],
            UserViewEvent::LIST => ['onUserListView', 0],
            UserViewEvent::DETAIL => ['onUserDetailView', 0],
        ];
    }

    /**
     * @param UserEvent $event
     *
     * @throws UuidException
     */
    public function onUserCreate(UserEvent $event)
    {
        $this->userService->createUserFromDto($event->getUserDto());
    }

    /**
     * @param UserEvent $event
     */
    public function onUserPreUpdate(UserEvent $event)
    {
        $form = $event->getForm();
        $form->remove('password');
        $form->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
        ]);
    }

    /**
     * @param UserEvent $event
     *
     * @throws UuidException
     */
    public function onUserUpdate(UserEvent $event)
    {
        $this->userService->updateUserFromDto($event->getUserDto(), $event->getUser());
    }

    /**
     * @param UserEvent $event
     */
    public function onUserDelete(UserEvent $event)
    {
        $this->userService->deleteUser($event->getUser());
    }

    /**
     * @param UserViewEvent $event
     */
    public function onUserCreateView(UserViewEvent $event)
    {
    }

    /**
     * @param UserViewEvent $event
     */
    public function onUserUpdateView(UserViewEvent $event)
    {
    }

    /**
     * @param UserViewEvent $event
     */
    public function onUserDeleteView(UserViewEvent $event)
    {
    }

    /**
     * @param UserViewEvent $event
     */
    public function onUserDetailView(UserViewEvent $event)
    {
    }

    /**
     * @param UserViewEvent $event
     */
    public function onUserListView(UserViewEvent $event)
    {
    }
}
