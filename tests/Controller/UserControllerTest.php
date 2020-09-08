<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\UuidService;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
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
            __DIR__.'/../DataFixtures/Fixtures.yaml',
        ]);

        foreach ($this->fixtures as $fixture) {
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
        /** @var User $user */
        $user = $this->fixtures['user_1'];

        $this->loginUser();

        $url = sprintf($url, $user->getUuid());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function loginUser()
    {
        /** @var User $user */
        $user = $this->fixtures['user_1'];

        // simulate $testUser being logged in
        $this->client->loginUser($user);
    }

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfUserIsNotInDB($url)
    {
        $this->loginUser();

        $url = sprintf($url, $this->uuidService->create());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideBadUserCredentials
     *
     * @param $badCredential array
     */
    public function testCreateUserWithBadCredential($badCredential)
    {
        $this->loginUser();

        $badCredential['user[language]'] = $this->fixtures['language_1']->getUuid();
        $crawler = $this->client->request('POST', '/admin/user/create/');
        $form = $crawler->selectButton('user[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testCreateUser()
    {
        $this->loginUser();

        $crawler = $this->client->request('POST', '/admin/user/create/');
        $form = $crawler->selectButton('user[save]')->form([
            'user[email]' => uniqid().'@test.com',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
            'user[language]' => $this->fixtures['language_1']->getUuid(),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/user/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Utilisateur créé avec succès.');
    }

    /**
     * @dataProvider provideBadUserCredentials
     *
     * @param $badCredential array
     */
    public function testUpdateUserWithBadCredentials($badCredential)
    {
        $this->loginUser();

        $badCredential['user[language]'] = $this->fixtures['language_1']->getUuid();
        $uri = sprintf('/admin/user/%s/update/', $this->fixtures['user_1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('user[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testUpdateUser()
    {
        $this->loginUser();

        $uri = sprintf('/admin/user/%s/update/', $this->fixtures['user_1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('user[save]')->form([
            'user[email]' => uniqid().'@test.com',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
            'user[language]' => $this->fixtures['language_1']->getUuid(),
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/user/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Utilisateur mis à jour avec succès.');
    }

    public function testDeleteUser()
    {
        $this->loginUser();

        $uri = sprintf('/admin/user/%s/delete/', $this->fixtures['user_2']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('delete[delete]')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/user/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Utilisateur supprimé avec succès.');
    }

    public function provideUrls()
    {
        yield ['/'];
        yield ['/admin/user/'];
        yield ['/admin/user/%s/'];
        yield ['/admin/user/create/'];
        yield ['/admin/user/%s/update/'];
        yield ['/admin/user/%s/delete/'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/admin/user/%s/'];
        yield ['/admin/user/%s/update/'];
        yield ['/admin/user/%s/delete/'];
    }

    public function provideBadUserCredentials()
    {
        yield [
            [
                'user[email]' => 'test@test.com',
                'user[password][first]' => '132456',
                'user[password][second]' => '1324655',
            ],
        ];
        yield [
            [
                'user[email]' => 'notAnEmail',
                'user[password][first]' => '132456',
                'user[password][second]' => '132456',
            ],
        ];
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
