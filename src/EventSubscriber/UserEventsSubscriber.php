<?php

namespace App\EventSubscriber;

use App\Event\User\UserEvent;
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
}
