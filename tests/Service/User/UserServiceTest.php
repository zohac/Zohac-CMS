<?php

namespace App\Tests\Service\User;

use App\Dto\User\UserDto;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserServiceTest extends KernelTestCase
{
    use FixturesTrait;

    private $fixtures;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userService = self::$container->get(UserService::class);
        $this->userRepository = self::$container->get(UserRepository::class);
        $this->roleRepository = self::$container->get(RoleRepository::class);
        $this->uuidService = self::$container->get(UuidService::class);
        $this->entityManager = self::$container->get(EntityManagerInterface::class);

        $this->loadFixtures();
    }

    public function loadFixtures()
    {
        $this->fixtures = $this->loadFixtureFiles([
            __DIR__.'/../../DataFixtures/Fixtures.yaml',
        ]);

        foreach ($this->fixtures as $fixture) {
            if ($fixture instanceof User) {
                $fixture->setUuid($this->uuidService->create());

                $role = $this->roleRepository->findOneBy(['id' => $this->fixtures['role_1']]);
                $fixture->addRole($role);

                $this->entityManager->persist($fixture);
            }
        }
        $this->entityManager->flush();

        return $this;
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
        /** @var Role[] $roles */
        $roles = $user->getRolesEntities();

        $roleInDto = array_flip($userDto->roles);

        $index = 0;
        foreach ($roles as $role) {
            $this->assertTrue(array_key_exists($role->getUuid(), $roleInDto));
            ++$index;
        }
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
        $userDto->roles = [$this->fixtures['role_1']->getUuid(), $this->fixtures['role_2']->getUuid()];
        $userDto->language = $this->fixtures['language_1']->getUuid();

        if ($withPassword) {
            $userDto->password = '0000';
        }

        return $userDto;
    }

    public function testUpdateUserFromDtoWithoutPassword()
    {
        $userDto = $this->getUserDto();
        /** @var User $user */
        $user = $this->fixtures['user_1'];

        // Refrech User
        $user = $this->userRepository->findOneById($user->getId());

        $user = $this->userService->updateUserFromDto($userDto, $user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $user->getUuid()
        );

        $this->assertEquals($user->getEmail(), $userDto->email);
        /** @var Role[] $roles */
        $roles = $user->getRolesEntities();

        $roleInDto = array_flip($userDto->roles);

        $index = 0;
        foreach ($roles as $role) {
            $this->assertTrue(array_key_exists($role->getUuid(), $roleInDto));
            ++$index;
        }
    }

    public function testUpdateUserFromDtoWithPassword()
    {
        $userDto = $this->getUserDto(true);
        $user = $this->fixtures['user_2'];

        // Refrech User
        $user = $this->userRepository->findOneById($user->getId());

        $user = $this->userService->updateUserFromDto($userDto, $user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertRegExp(
            '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/',
            $user->getUuid()
        );
        $this->assertEquals($user->getEmail(), $userDto->email);
        /** @var Role[] $roles */
        $roles = $user->getRolesEntities();

        $roleInDto = array_flip($userDto->roles);

        $index = 0;
        foreach ($roles as $role) {
            $this->assertTrue(array_key_exists($role->getUuid(), $roleInDto));
            ++$index;
        }
    }

    public function testDeleteUser()
    {
        $user = $this->fixtures['user_1'];
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
        $this->fixtures = null;
        $this->userService = null;
        $this->userRepository = null;
    }
}
