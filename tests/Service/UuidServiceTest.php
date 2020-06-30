<?php

namespace App\Tests\Service;

use App\Exception\UuidException;
use App\Service\UuidService;
use PHPUnit\Framework\TestCase;

class UuidServiceTest extends TestCase
{
    public function testCreate()
    {
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            (new UuidService())->create()
        );
    }

    public function testCreateWhenUuidIsInvalide()
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

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('L\'application ne parvient pas à générer un uuid v4 valide.');
        $uuidService->create();
    }

    public function testCreateWhenFunctionUuidCreateDoesNotExist()
    {
        $uuidService = $this->getMockBuilder(UuidService::class)
            ->setMethodsExcept(['create'])
            ->setMethods(['isValid', 'functionExist'])
            ->getMock();

        $uuidService->expects($this->exactly(1))
            ->method('functionExist')
            ->willReturn(false);

        $this->expectException(UuidException::class);
        $this->expectExceptionMessage('La fonction uuid_create de php n\'existe pas.');
        $uuidService->create();
    }

    public function testIsValidWithAValidUuid()
    {
        $uuidService = new UuidService();
        $uuid = $uuidService->create();

        $this->assertTrue($uuidService->isValid($uuid));
    }

    public function testIsValidWithAnInvalidUuid()
    {
        $this->assertFalse((new UuidService())->isValid('unvalid-uuid'));
    }

    public function testFunctionExistWithAnInvalidFunction()
    {
        $this->assertFalse((new UuidService())->functionExist('unvalid-function'));
    }

    public function testFunctionExistWithAnValidFunction()
    {
        $this->assertTrue((new UuidService())->functionExist('uuid_create'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
    }
}
