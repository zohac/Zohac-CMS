<?php

namespace App\Tests\Service;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Exception\UuidException;
use App\Repository\UserRepository;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use App\Service\User\UserService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EntityServiceTest extends KernelTestCase
{
    /**
     * @var EntityService
     */
    private $entityService;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->entityService = self::$container->get(EntityService::class);
    }

    public function testGetUuid()
    {
        $uuid = $this->entityService->getUuid();

        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $uuid
        );
    }

    public function testExceptionWhenGetUuid()
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uuidService = $this->getMockBuilder(UuidService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityService = new EntityService($entityManager, $uuidService);

        $uuidService->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $this->expectException(UuidException::class);

        $entityService->getUuid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
    }
}
