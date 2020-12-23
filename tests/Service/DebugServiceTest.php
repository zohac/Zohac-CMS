<?php

namespace App\Tests\Service;

use App\Service\DebugService;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DebugServiceTest extends TestCase
{
    /**
     * @var MockObject|FlashBagInterface
     */
    private $flashBag;

    /**
     * @throws RuntimeException
     */
    public function setUp(): void
    {
        $this->flashBag = $this->getMockBuilder(FlashBagInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @throws RuntimeException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testDisplayDebugMessageWhenIsNotDebug()
    {
        $debugService = $this->getDebugService();

        $this->flashBag->expects($this->never())->method('add');
        $return = $debugService->displayDebugMessage();
        $this->assertInstanceOf(DebugService::class, $return);
    }

    /**
     * @throws RuntimeException
     */
    public function getDebugService(bool $isDebug = false): DebugService
    {
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
