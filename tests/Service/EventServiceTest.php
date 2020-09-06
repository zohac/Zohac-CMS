<?php

namespace App\Tests\Service;

use App\Event\User\UserEvent;
use App\Exception\EventException;
use App\Interfaces\Event\EventInterface;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventServiceTest extends WebTestCase
{
    /**
     * @var EventService|null
     */
    private $eventService = null;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->eventService = self::$container->get(EventService::class);
    }

    public function testGetEventService()
    {
        $this->assertInstanceOf(EventService::class, $this->eventService);
    }

    public function testGetEvent()
    {
        $event = $this->eventService->getEvent(UserEvent::CREATE);
        $this->assertEquals(UserEvent::CREATE, $event::CREATE);
    }

    public function testGetEvents()
    {
        $events = $this->eventService->getEvents();

        foreach ($events as $event) {
            $this->assertInstanceOf(EventInterface::class, $event);
        }
    }

    public function testEventException()
    {
        $this->expectException(EventException::class);

        $this->eventService->getEvent('event.test');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->eventService = null;
    }
}
