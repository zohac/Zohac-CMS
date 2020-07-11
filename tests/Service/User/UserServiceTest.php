<?php

namespace App\Tests\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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

    private $uuidService;

    private $entityManager;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userService = self::$container->get(UserService::class);
        $this->userRepository = self::$container->get(UserRepository::class);
        $this->uuidService = self::$container->get(UuidService::class);
        $this->entityManager = self::$container->get(EntityManagerInterface::class);

        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = $this->loadFixtureFiles([
            __DIR__.'/../../DataFixtures/UserFixtures.yaml',
        ]);

        foreach ($this->users as $user) {
            $user->setUuid($this->uuidService->create());

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
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
        $userDto->locale = 'en';

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

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->users = null;
        $this->userService = null;
        $this->userRepository = null;
    }
}
