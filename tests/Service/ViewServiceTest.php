<?php

namespace App\Tests\Service;

use App\Service\ViewService;
use PHPUnit\Framework\TestCase;

class ViewServiceTest extends TestCase
{
    public function testSetData()
    {
        $viewService = new ViewService();
        $return = $viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('view', $viewService->getView());
        $this->assertTrue(is_array($viewService->getOptions()));
    }

    public function testSetView()
    {
        $viewService = new ViewService();
        $return = $viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $return = $viewService->setView('view-2');
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('view-2', $viewService->getView());
    }

    public function testSetOptions()
    {
        $viewService = new ViewService();
        $return = $viewService->setData('view', ['an option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $return = $viewService->setOptions(['an another option']);
        $this->assertInstanceOf(ViewService::class, $return);

        $this->assertEquals('an another option', $viewService->getOptions()[0]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
    }
}
