<?php

namespace App\Tests\Service;

use App\Service\DebugService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DebugServiceTest extends TestCase
{
    private $flashBag;

    public function testDisplayDebugMessageWhenIsNotDebug()
    {
        $debugService = $this->getDebugService();

        $this->flashBag->expects($this->never())->method('add');
        $return = $debugService->displayDebugMessage();
        $this->assertInstanceOf(DebugService::class, $return);
    }

    /**
     * @param bool $isDebug
     *
     * @return DebugService
     */
    public function getDebugService(bool $isDebug = false): DebugService
    {
        $this->flashBag = $this->getMockBuilder(FlashBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new DebugService($this->flashBag, $requestStack, $isDebug);
    }

    public function testDisplayDebugMessageWhenIsDebug()
    {
        $debugService = $this->getDebugService(true);

        $this->flashBag->expects($this->once())->method('add');
        $return = $debugService->displayDebugMessage();
        $this->assertInstanceOf(DebugService::class, $return);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
    }
}
