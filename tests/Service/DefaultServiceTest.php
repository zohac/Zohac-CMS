<?php

namespace App\Tests\Service;

use App\Event\User\UserEvent;
use App\Event\User\UserViewEvent;
use App\Interfaces\Event\EventInterface;
use App\Service\DefaultService;
use App\Service\ViewService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DefaultServiceTest extends WebTestCase
{
    /**
     * @var DefaultService|null
     */
    private $defaultService = null;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->defaultService = self::$container->get(DefaultService::class);
    }

    public function testGetSerializer()
    {
        $this->assertInstanceOf(SerializerInterface::class, $this->defaultService->getSerializer());
    }

    public function testGetEventDispatcher()
    {
        $this->assertInstanceOf(EventDispatcherInterface::class, $this->defaultService->getEventDispatcher());
    }

    public function testGetValidator()
    {
        $this->assertInstanceOf(ValidatorInterface::class, $this->defaultService->getValidator());
    }

    public function testGetEvent()
    {
        $this->assertInstanceOf(EventInterface::class, $this->defaultService->getEvent(UserEvent::CREATE));
    }

    public function testDispatchEvent()
    {
        // On crée notre évènement
        $viewService = new ViewService();

        $this->defaultService->dispatchEvent(UserViewEvent::LIST, ['viewService' => $viewService]);

        /** @var UserViewEvent $event */
        $event = $this->defaultService->getEvent(UserViewEvent::LIST);
        $this->assertInstanceOf(ViewService::class, $event->getViewService());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->defaultService = null;
    }
}
