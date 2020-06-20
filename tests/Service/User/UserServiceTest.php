<?php

namespace App\Tests\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Exception\UuidException;
use App\Repository\UserRepository;
use App\Service\EventService;
use App\Service\User\UserService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var User[]
     */
    private $users;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userService = self::$container->get(UserService::class);
        $this->userRepository = self::$container->get(UserRepository::class);

        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = $this->loadFixtureFiles([
            __DIR__.'/../../DataFixtures/UserFixtures.yaml',
        ]);
    }

    public function testCreateUserDtoFromUser()
    {
        $user = $this->users['user1'];

        $userDto = $this->userService->createUserDtoFromUser($user);

        $this->assertInstanceOf(UserDto::class, $userDto);
        $this->assertEquals($user->getUuid(), $userDto->uuid);
        $this->assertEquals($user->getEmail(), $userDto->email);
        $this->assertEquals($user->getRoles(), $userDto->roles);
    }

    public function testCreateUserFromDto()
    {
        $userDto = $this->getUserDto(true);

        $user = $this->userService->createUserFromDto($userDto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $user->getUuid()
        );
        $this->assertEquals($user->getEmail(), $userDto->email);
        $this->assertEquals($user->getRoles(), $userDto->roles);
    }

    /**
     * @param bool|null $withPassword
     *
     * @return UserDto
     */
    public function getUserDto(?bool $withPassword = false): UserDto
    {
        $userDto = new UserDto();
        $userDto->email = uniqid().'@test.com';
        $userDto->roles = ['ROLE_TEST', 'ROLE_USER'];

        if ($withPassword) {
            $userDto->password = '0000';
        }

        return $userDto;
    }

    public function testUpdateUserFromDtoWithoutPassword()
    {
        $userDto = $this->getUserDto();
        $user = $this->users['user1'];

        // Refrech User
        $user = $this->userRepository->findOneById($user->getId());

        $user = $this->userService->updateUserFromDto($userDto, $user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $user->getUuid()
        );
        $this->assertEquals($user->getEmail(), $userDto->email);
        $this->assertEquals($user->getRoles(), $userDto->roles);
    }

    public function testUpdateUserFromDtoWithPassword()
    {
        $userDto = $this->getUserDto(true);
        $user = $this->users['user2'];

        // Refrech User
        $user = $this->userRepository->findOneById($user->getId());

        $user = $this->userService->updateUserFromDto($userDto, $user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $user->getUuid()
        );
        $this->assertEquals($user->getEmail(), $userDto->email);
        $this->assertEquals($user->getRoles(), $userDto->roles);
    }

    public function testDeleteUser()
    {
        $user = $this->users['user1'];
        $userId = $user->getId();
        // Refrech User
        $user = $this->userRepository->findOneById($userId);

        $return = $this->userService->deleteUser($user);
        $this->assertInstanceOf(UserService::class, $return);

        $user = $this->userRepository->findOneById($userId);
        $this->assertEquals(null, $user);
    }

    public function testGetUuid()
    {
        $uuid = $this->userService->getUuid();

        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $uuid
        );
    }

    public function testExceptionWhenGetUuid()
    {
        $eventService = $this->getMockBuilder(EventService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $passwordEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uuidService = $this->getMockBuilder(UuidService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userService = new UserService(
            $eventService,
            $passwordEncoder,
            $entityManager,
            $uuidService
        );

        $uuidService->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $this->expectException(UuidException::class);

        $userService->getUuid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->users = null;
        $this->userService = null;
        $this->userRepository = null;
    }
}
