<?php

namespace App\Tests\Factory;

use App\Exception\UuidException;
use App\Factory\UserFactory;
use App\Service\UuidService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserFactoryTest extends KernelTestCase
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userFactory = self::$container->get(UserFactory::class);
    }

    public function testGetUuid()
    {
        $uuid = $this->userFactory->getUuid();

        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $uuid
        );
    }

    public function testExceptionWhenGetUuid()
    {
        $uuidService = $this->getMockBuilder(UuidService::class)
            ->setMethodsExcept(['create'])
            ->setMethods(['isValid', 'functionExist'])
            ->getMock();

        $uuidService->expects($this->exactly(6))
            ->method('isValid')
            ->willReturn(false);

        $uuidService->expects($this->exactly(1))
            ->method('functionExist')
            ->willReturn(true);

        $userFactory = new UserFactory($uuidService);

        $this->expectException(UuidException::class);

        $userFactory->getUuid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks

        $this->userFactory = null;
    }
}
