<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Menu;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\UuidService;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MenuControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    private $fixtures;

    /**
     * @var KernelBrowser|null
     */
    private $client = null;

    /**
     * @var UuidService|null
     */
    private $uuidService = null;

    /**
     * @var RoleRepository|null
     */
    private $roleRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->uuidService = self::$container->get(UuidService::class);
        $this->roleRepository = self::$container->get(RoleRepository::class);

        $this->loadFixtures();
    }

    public function loadFixtures()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $this->fixtures = $this->loadFixtureFiles([
            __DIR__.'/../../DataFixtures/Fixtures.yaml',
        ]);

        foreach ($this->fixtures as $fixture) {
            if ($fixture instanceof Menu) {
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

    public function loginUser()
    {
        $this->client->disableReboot();

        /** @var User $user */
        $user = $this->fixtures['user_1'];

        // simulate $testUser being logged in
        $this->client->loginUser($user);
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $this->loginUser();

//        $url = sprintf($url, $this->fixtures['menu_1']->getUuid());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfMenuIsNotInDB($url)
    {
        $this->loginUser();

        $url = sprintf($url, $this->uuidService->create());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideUrls()
    {
        yield ['/admin/menu/'];
//        yield ['/admin/menu/%s/'];
        yield ['/admin/menu/create/'];
//        yield ['/admin/menu/%s/update/'];
//        yield ['/admin/menu/%s/delete/'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/admin/menu/%s/'];
        yield ['/admin/menu/%s/update/'];
        yield ['/admin/menu/%s/delete/'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->client = null;
        $this->fixtures = null;
        $this->uuidService = null;
    }
}
