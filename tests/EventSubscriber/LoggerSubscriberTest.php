<?php

namespace App\Tests\EventSubscriber;

use App\Dto\User\UserDto;
use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\EventSubscriber\LoggerSubscriber;
use App\Service\DebugService;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class LoggerSubscriberTest extends TestCase
{
    private $debugService;

    private $logger;

    public function setUp(): void
    {
        $this->debugService = $this->getMockBuilder(DebugService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

//    public function testEventSubscription()
//    {
//        $this->assertArrayHasKey(UserEvent::PRE_CREATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::CREATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::POST_CREATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::PRE_UPDATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::UPDATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::POST_UPDATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::PRE_DELETE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::DELETE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserEvent::POST_DELETE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserViewEvent::LIST, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserViewEvent::DETAIL, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserViewEvent::CREATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserViewEvent::UPDATE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(UserViewEvent::DELETE, LoggerSubscriber::getSubscribedEvents());
//        $this->assertArrayHasKey(KernelEvents::EXCEPTION, LoggerSubscriber::getSubscribedEvents());
//    }

    public function testOnEvents()
    {
        $loggerSubscriber = new LoggerSubscriber($this->logger, $this->debugService);

        // On crée notre évènement
        $event = new UserEvent();
        $userDto = new UserDto();
        $event->setData(['userDto' => $userDto]);
        $event->setEventCalled(UserEvent::CREATE);

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($loggerSubscriber);
        $dispatcher->dispatch($event, $event::CREATE);

        $this->logger->expects($this->once())->method('debug');
        $this->debugService->expects($this->once())->method('getContext');

        $loggerSubscriber->onEvent($event);
    }

    public function testOnException()
    {
        $loggerSubscriber = new LoggerSubscriber($this->logger, $this->debugService);

        // On crée notre évènement
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ExceptionEvent($kernel, new Request(), 1, new Exception('Hello world'));

        // On dispatch notre évènement en ayant notre subscriber dans le dispatcher.
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($loggerSubscriber);
        $dispatcher->dispatch($event);

        $this->logger->expects($this->once())->method('error');
        $this->debugService->expects($this->once())->method('displayDebugMessage');

        $loggerSubscriber->onException($event);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->debugService = null;
        $this->logger = null;
    }
}
