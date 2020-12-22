<?php

namespace App\Tests\Service;

use App\Service\DebugService;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DebugServiceTest extends KernelTestCase
{
    private $flashBag;

    /**
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
     * @param bool $isDebug
     *
     * @return DebugService
     *
     * @throws RuntimeException
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

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
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

        $this->flashBag = null;
    }
}
