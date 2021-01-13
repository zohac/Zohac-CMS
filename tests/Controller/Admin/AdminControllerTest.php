<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Language;
use App\Entity\User;
use App\Exception\UuidException;
use App\Repository\RoleRepository;
use App\Service\UuidService;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    private $fixtures;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var KernelBrowser|null
     */
    private $client = null;

    /**
     * @var UuidService|null
     */
    private $uuidService = null;

    /**
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws UuidException
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->uuidService = self::$container->get(UuidService::class);
        $this->roleRepository = self::$container->get(RoleRepository::class);

        $this->loadFixtures();
    }

    /**
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws UuidException
     */
    public function loadFixtures()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $this->fixtures = $this->loadFixtureFiles([
            __DIR__.'/../../DataFixtures/Fixtures.yaml',
        ]);

        foreach ($this->fixtures as $fixture) {
            if ($fixture instanceof Language) {
                $fixture->setUuid($this->uuidService->create());

                $entityManager->persist($fixture);
            }
            if ($fixture instanceof User) {
                $fixture->setUuid($this->uuidService->create());

                $role = $this->roleRepository->findOneBy(['id' => $this->fixtures['role_1']]);
                $fixture->addRole($role);

                $entityManager->persist($fixture);
            }
        }
        $entityManager->flush();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $this->loginUser();

        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->getCookieJar()->clear();
    }

    public function loginUser()
    {
        $this->client->disableReboot();

        /** @var User $user */
        $user = $this->fixtures['user_1'];

        // simulate $testUser being logged in
        $this->client->loginUser($user);
    }

    public function provideUrls()
    {
        yield ['/admin/'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->client = null;
        $this->fixtures = null;
        $this->uuidService = null;
        $this->roleRepository = null;
    }
}
