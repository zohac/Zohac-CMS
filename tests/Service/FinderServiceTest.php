<?php

namespace App\Tests\Service;

use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\Interfaces\Event\EventInterface;
use App\Service\FinderService;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class FinderServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetEventsByInterface()
    {
        $events = FinderService::getEventsByInterface(EventInterface::class);

        $this->assertContains(UserEvent::PRE_CREATE, $events);
        $this->assertContains(UserEvent::CREATE, $events);
        $this->assertContains(UserEvent::POST_CREATE, $events);
        $this->assertContains(UserEvent::PRE_UPDATE, $events);
        $this->assertContains(UserEvent::UPDATE, $events);
        $this->assertContains(UserEvent::POST_UPDATE, $events);
        $this->assertContains(UserEvent::PRE_DELETE, $events);
        $this->assertContains(UserEvent::DELETE, $events);
        $this->assertContains(UserEvent::POST_DELETE, $events);
        $this->assertContains(UserViewEvent::LIST, $events);
        $this->assertContains(UserViewEvent::DETAIL, $events);
        $this->assertContains(UserViewEvent::CREATE, $events);
        $this->assertContains(UserViewEvent::UPDATE, $events);
        $this->assertContains(UserViewEvent::DELETE, $events);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
    }
}
