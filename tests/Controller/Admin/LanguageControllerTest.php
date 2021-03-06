<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Language;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\UuidService;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LanguageControllerTest extends WebTestCase
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

        $url = sprintf($url, $this->fixtures['language_1']->getUuid());
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

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfLanguageIsNotInDB($url)
    {
        $this->loginUser();

        $url = sprintf($url, $this->uuidService->create());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->getCookieJar()->clear();
    }

    /**
     * @dataProvider provideBadLanguageCredentials
     *
     * @param $badCredential array
     */
    public function testCreateLanguageWithBadCredential($badCredential)
    {
        $this->loginUser();

        $crawler = $this->client->request('POST', '/admin/language/create/');
        $form = $crawler->selectButton('language[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');

        $this->client->getCookieJar()->clear();
    }

    public function testCreateLanguage()
    {
        $this->loginUser();

        $crawler = $this->client->request('POST', '/admin/language/create/');
        $form = $crawler->selectButton('language[save]')->form([
            'language[name]' => 'German',
            'language[alternateName]' => 'Allemand',
            'language[description]' => 'langue allemande',
            'language[iso6391]' => 'de',
            'language[iso6392T]' => 'ger',
            'language[iso6392B]' => 'deu',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue créée avec succès.');

        $this->client->getCookieJar()->clear();
    }

    /**
     * @dataProvider provideBadLanguageCredentials
     *
     * @param $badCredential array
     */
    public function testUpdateLanguageWithBadCredentials($badCredential)
    {
        $this->loginUser();

        $uri = sprintf('/admin/language/%s/update/', $this->fixtures['language_1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('language[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');

        $this->client->getCookieJar()->clear();
    }

    public function testUpdateLanguage()
    {
        $this->loginUser();

        $uri = sprintf('/admin/language/%s/update/', $this->fixtures['language_1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('language[save]')->form([
            'language[name]' => 'test_update',
            'language[alternateName]' => 'Test_update',
            'language[description]' => 'a language test update',
            'language[iso6391]' => 'fr',
            'language[iso6392T]' => 'fre',
            'language[iso6392B]' => 'fra',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue mise à jour avec succès.');

        $this->client->getCookieJar()->clear();
    }

    public function testDeleteLanguage()
    {
        $this->loginUser();

        $uri = sprintf('/admin/language/%s/delete/', $this->fixtures['language_1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('delete[delete]')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue supprimée avec succès.');

        $this->client->getCookieJar()->clear();
    }

    public function provideUrls()
    {
        yield ['/admin/language/'];
        yield ['/admin/language/%s/'];
        yield ['/admin/language/create/'];
        yield ['/admin/language/%s/update/'];
        yield ['/admin/language/%s/delete/'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/admin/language/%s/'];
        yield ['/admin/language/%s/update/'];
        yield ['/admin/language/%s/delete/'];
    }

    public function provideBadLanguageCredentials()
    {
        yield [
            [
                'language[name]' => '',
                'language[alternateName]' => 'Test',
                'language[iso6391]' => 't',
                'language[iso6392T]' => 't',
                'language[iso6392B]' => 't',
            ],
        ];
        yield [
            [
                'language[name]' => '',
                'language[alternateName]' => 'Test',
                'language[iso6391]' => 't',
                'language[iso6392T]' => 't',
                'language[iso6392B]' => 't',
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
