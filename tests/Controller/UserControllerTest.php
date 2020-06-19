<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Service\User\UserService;
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
     * @var User[]
     */
    private $users;

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

        $this->loadUsers();
    }

    public function loadUsers()
    {
        $uuidService = self::$container->get(UserService::class);
        /** @var ObjectManager $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $this->users = $this->loadFixtureFiles([
            __DIR__.'/../DataFixtures/UserFixtures.yaml',
        ]);

        foreach ($this->users as $key => $user) {
            $user->setUuid($uuidService->getUuid());
            $entityManager->persist($user);

            $this->users[$key] = $user;
        }
        $entityManager->flush();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $url = sprintf($url, $this->users['user1']->getUuid());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfUserIsNotInDB($url)
    {
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
        $crawler = $this->client->request('POST', '/users/create');
        $form = $crawler->selectButton('user[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testCreateUser()
    {
        $crawler = $this->client->request('POST', '/users/create');
        $form = $crawler->selectButton('user[save]')->form([
            'user[email]' => uniqid().'@test.com',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
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
        $uri = sprintf('/users/%s/update', $this->users['user1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('user[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testUpdateUser()
    {
        $uri = sprintf('/users/%s/update', $this->users['user1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('user[save]')->form([
            'user[email]' => uniqid().'@test.com',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Utilisateur mis à jour avec succès.');
    }

    public function testDeleteUser()
    {
        $uri = sprintf('/users/%s/delete', $this->users['user1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('delete[delete]')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Utilisateur supprimé avec succès.');
    }

    public function provideUrls()
    {
        yield ['/'];
        yield ['/users'];
        yield ['/users/%s'];
        yield ['/users/create'];
        yield ['/users/%s/update'];
        yield ['/users/%s/delete'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/users/%s'];
        yield ['/users/%s/update'];
        yield ['/users/%s/delete'];
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
        $this->users = null;
    }
}
