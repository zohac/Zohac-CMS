<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use function count;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var User[]
     */
    private $users;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userRepository = self::$container->get(UserRepository::class);

        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = $this->loadFixtureFiles([
            __DIR__ . '/../DataFixtures/Fixtures.yaml',
        ]);
    }

    public function testCountUser()
    {
        $numberOfUsers = $this->userRepository->count([]);

        $this->assertEquals(10, $numberOfUsers);
    }

    public function testFindAllNotArchived()
    {
        $users = $this->userRepository->findAllNotArchived();

        $this->assertEquals(10, count($users));
    }

    public function testUpgradePassword()
    {
        $user = $this->users['user1'];
        // Refresh the user from DB
        $user = $this->userRepository->findOneById($user->getId());

        $this->assertEquals($this->users['user1']->getPassword(), $user->getPassword());

        $this->userRepository->upgradePassword($user, '1111');
        $this->assertEquals('1111', $user->getPassword());
    }

    public function testUpgradePasswordWithoutUser()
    {
        $user = new class() implements UserInterface {
            public function getRoles()
            {
            }

            public function getPassword()
            {
            }

            public function getSalt()
            {
            }

            public function getUsername()
            {
            }

            public function eraseCredentials()
            {
            }
        };

        $this->expectException(UnsupportedUserException::class);

        $this->userRepository->upgradePassword($user, '1111');
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
