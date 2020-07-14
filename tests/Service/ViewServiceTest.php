<?php

namespace App\Tests\Service;

use App\Interfaces\Event\EventInterface;
use App\Service\ViewService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ViewServiceTest extends KernelTestCase
{
    /**
     * @var ViewService
     */
    private $viewService;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->viewService = self::$container->get(ViewService::class);
    }

    public function testSetData()
    {
        $return = $this->viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('view', $this->viewService->getView());
        $this->assertTrue(is_array($this->viewService->getOptions()));
    }

    public function testSetView()
    {
        $return = $this->viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $return = $this->viewService->setView('view-2');
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('view-2', $this->viewService->getView());
    }

    public function testSetOptions()
    {
        $return = $this->viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $return = $this->viewService->setOptions(['an another option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('an another option', $this->viewService->getOptions()[0]);
    }

    public function testGetViewEvents()
    {
        $events = $this->viewService->getViewEvents();

        foreach ($events as $event) {
            $this->assertInstanceOf(EventInterface::class, $event);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->viewService = null;
    }
}
