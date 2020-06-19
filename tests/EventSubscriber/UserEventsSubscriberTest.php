<?php

namespace App\Tests\EventSubscriber;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\EventSubscriber\UserEventsSubscriber;
use App\Service\User\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;

class UserEventsSubscriberTest extends TestCase
{
    private $userService;

    public function setUp(): void
    {
        $this->userService = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testEventSubscription()
    {
        $this->assertArrayHasKey(UserEvent::CREATE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserEvent::PRE_UPDATE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserEvent::UPDATE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserEvent::DELETE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserViewEvent::LIST, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserViewEvent::DETAIL, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserViewEvent::CREATE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserViewEvent::UPDATE, UserEventsSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserViewEvent::DELETE, UserEventsSubscriber::getSubscribedEvents());
    }

    public function testOnUserCreate()
    {
        $userEventsSubscriber = new UserEventsSubscriber($this->userService);

        // On crée notre évènement
        $event = new UserEvent();
        $userDto = new UserDto();
        $event->setData(['userDto' => $userDto]);

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($userEventsSubscriber);
        $dispatcher->dispatch($event, $event::CREATE);

        $this->userService->expects($this->once())->method('createUserFromDto');
        $userEventsSubscriber->onUserCreate($event);
    }

    public function testOnUserPreUpdate()
    {
        $userEventsSubscriber = new UserEventsSubscriber($this->userService);

        // On crée notre évènement
        $event = new UserEvent();
        $form = $this->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->setData(['form' => $form]);

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($userEventsSubscriber);
        $dispatcher->dispatch($event, UserEvent::PRE_UPDATE);

        $form->expects($this->once())->method('remove');
        $form->expects($this->once())->method('add');

        $userEventsSubscriber->onUserPreUpdate($event);

        $data = $event->getData();
        $this->assertInstanceOf(FormInterface::class, $data['form']);
    }

    public function testOnUserUpdate()
    {
        $userEventsSubscriber = new UserEventsSubscriber($this->userService);

        // On crée notre évènement
        $event = new UserEvent();
        $userDto = new UserDto();
        $user = new User();
        $event->setData([
            'userDto' => $userDto,
            'user' => $user,
        ]);

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($userEventsSubscriber);
        $dispatcher->dispatch($event, $event::UPDATE);

        $this->userService->expects($this->once())->method('updateUserFromDto');
        $userEventsSubscriber->onUserUpdate($event);
    }

    public function testOnUserDelete()
    {
        $userEventsSubscriber = new UserEventsSubscriber($this->userService);

        // On crée notre évènement
        $event = new UserEvent();
        $user = new User();
        $event->setData(['user' => $user]);

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($userEventsSubscriber);
        $dispatcher->dispatch($event, $event::DELETE);

        $this->userService->expects($this->once())->method('deleteUser');
        $userEventsSubscriber->onUserDelete($event);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->userService = null;
    }
}
