<?php

namespace App\Tests\Service;

use App\Service\ViewService;
use PHPUnit\Framework\TestCase;

class ViewServiceTest extends TestCase
{
    /**
     * @var ViewService
     */
    private $viewService;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->viewService = self::$container->get(ViewService::class);

        $this->loadUsers();
    }

    public function testSetData()
    {
        $return = $this->viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('view', $this->viewService->getView());
        $this->assertTrue(is_array($viewService->getOptions()));
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

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->viewService = null;
    }
}
