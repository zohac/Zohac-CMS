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
            UserEvent::PRE_CREATE => ['onUserPreCreate', 0],
            UserEvent::CREATE => ['onUserCreate', 0],
            UserEvent::POST_CREATE => ['onUserPostCreate', 0],
            UserEvent::PRE_UPDATE => ['onUserPreUpdate', 0],
            UserEvent::UPDATE => ['onUserUpdate', 0],
            UserEvent::POST_UPDATE => ['onUserPostUpdate', 0],
            UserEvent::PRE_DELETE => ['onUserPreDelete', 0],
            UserEvent::DELETE => ['onUserDelete', 0],
            UserEvent::POST_DELETE => ['onUserPostDelete', 0],
            UserViewEvent::CREATE => ['onUserCreateView', 0],
            UserViewEvent::UPDATE => ['onUserUpdateView', 0],
            UserViewEvent::DELETE => ['onUserDeleteView', 0],
            UserViewEvent::LIST => ['onUserListView', 0],
            UserViewEvent::DETAIL => ['onUserDetailView', 0],
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function onUserPreCreate(UserEvent $event)
    {
        dump('pre.create');
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
    public function onUserPostCreate(UserEvent $event)
    {
        dump('post.create');
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
    public function onUserPostUpdate(UserEvent $event)
    {
        dump('post.update');
    }

    /**
     * @param UserEvent $event
     */
    public function onUserPreDelete(UserEvent $event)
    {
        dump('pre.delete');
    }

    /**
     * @param UserEvent $event
     */
    public function onUserDelete(UserEvent $event)
    {
        $this->userService->deleteUser($event->getUser());
    }

    /**
     * @param UserEvent $event
     */
    public function onUserPostDelete(UserEvent $event)
    {
        dump('post.delete');
    }

    public function onUserCreateView(UserViewEvent $event)
    {
        dump('create.view');
    }

    public function onUserUpdateView(UserViewEvent $event)
    {
        dump('update.view');
    }

    public function onUserDeleteView(UserViewEvent $event)
    {
        dump('delete.view');
    }

    public function onUserDetailView(UserViewEvent $event)
    {
        dump('detail.view');
    }

    public function onUserListView(UserViewEvent $event)
    {
        dump('list.view');
    }
}
