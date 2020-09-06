<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    private $fixtures;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp(): void
    {
        self::bootKernel(['debug' => 0]);
        $this->userRepository = self::$container->get(UserRepository::class);

        $this->loadFixtures();
    }

    public function loadFixtures()
    {
        $this->fixtures = $this->loadFixtureFiles([
            __DIR__.'/../DataFixtures/Fixtures.yaml',
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

        $this->assertCount(10, $users);
    }

    public function testUpgradePassword()
    {
        $user = $this->fixtures['user_1'];
        // Refresh the user from DB
        $user = $this->userRepository->findOneById($user->getId());

        $this->assertEquals($this->fixtures['user_1']->getPassword(), $user->getPassword());

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
        $this->fixtures = null;
        $this->userRepository = null;
    }
}
