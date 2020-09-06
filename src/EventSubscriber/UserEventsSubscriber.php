<?php

namespace App\EventSubscriber;

use App\Event\User\UserEvent;
use App\Exception\HydratorException;
use App\Service\User\UserService;
use ReflectionException;
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
            UserEvent::SOFT_DELETE => ['onUserSoftDelete', 0],
        ];
    }

    /**
     * @param UserEvent $event
     *
     * @throws HydratorException
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
            'label' => false,
            'type' => PasswordType::class,
            'first_options' => ['label' => 'password'],
            'second_options' => ['label' => 'repeat password'],
            'required' => false,
        ]);
    }

    /**
     * @param UserEvent $event
     *
     * @throws HydratorException
     */
    public function onUserUpdate(UserEvent $event)
    {
        $this->userService->updateUserFromDto($event->getUserDto(), $event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws ReflectionException
     */
    public function onUserDelete(UserEvent $event)
    {
        $this->userService->deleteUser($event->getUser());
    }

    /**
     * @param UserEvent $event
     *
     * @throws ReflectionException
     */
    public function onUserSoftDelete(UserEvent $event)
    {
        $this->userService->deleteSoftUser($event->getUser());
    }
}
