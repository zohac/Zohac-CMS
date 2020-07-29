<?php

namespace App\Tests\Service;

use App\Service\TranslatorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TranslatorServiceTest extends KernelTestCase
{
    /**
     * @var TranslatorService
     */
    private $translatorService;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->translatorService = self::$container->get(TranslatorService::class);
    }

    public function testSetAndGetLocal()
    {
        $this->translatorService->setLocale('fr');

        $this->assertEquals('fr', $this->translatorService->getLocale());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->translatorService = null;
    }
}
