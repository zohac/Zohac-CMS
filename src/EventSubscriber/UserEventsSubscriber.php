<?php

namespace App\EventSubscriber;

use App\Event\User\UserCreateEvent;
use App\Event\User\UserPostCreateEvent;
use App\Event\User\UserPostUpdateEvent;
use App\Event\User\UserPreCreateEvent;
use App\Event\User\UserPreUpdateEvent;
use App\Event\User\UserUpdateEvent;
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
            UserPreCreateEvent::class => ['onUserPreCreate', 0],
            UserCreateEvent::class => ['onUserCreate', 0],
            UserPostCreateEvent::class => ['onUserPostCreate', 0],
            UserPreUpdateEvent::class => ['onUserPreUpdate', 0],
            UserUpdateEvent::class => ['onUserUpdate', 0],
            UserPostUpdateEvent::class => ['onUserPostUpdate', 0],
        ];
    }

    /**
     * @param UserPreCreateEvent $event
     */
    public function onUserPreCreate(UserPreCreateEvent $event)
    {
        dump($event);
    }

    /**
     * @param UserCreateEvent $event
     *
     * @throws UuidException
     */
    public function onUserCreate(UserCreateEvent $event)
    {
        $this->userService->createUserFromDto($event->getUserDto());
    }

    /**
     * @param UserPostCreateEvent $event
     */
    public function onUserPostCreate(UserPostCreateEvent $event)
    {
        dump($event);
    }

    /**
     * @param UserPreUpdateEvent $event
     */
    public function onUserPreUpdate(UserPreUpdateEvent $event)
    {
        $form = $event->getForm();
        $form->remove('password');
        $form->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
        ]);
    }

    /**
     * @param UserUpdateEvent $event
     *
     * @throws UuidException
     */
    public function onUserUpdate(UserUpdateEvent $event)
    {
        $this->userService->updateUserFromDto($event->getUserDto(), $event->getUser());
    }

    /**
     * @param UserPostUpdateEvent $event
     */
    public function onUserPostUpdate(UserPostUpdateEvent $event)
    {
        dump($event);
    }
}
