<?php

namespace App\EventSubscriber;

use App\Event\User\UserCreateEvent;
use App\Event\User\UserPostCreateEvent;
use App\Event\User\UserPreCreateEvent;
use App\Service\User\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
}
