<?php

namespace App\Tests\Controller;

use App\Entity\Role;
use App\Service\UuidService;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RoleControllerTest extends WebTestCase
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

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->uuidService = self::$container->get(UuidService::class);

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
            if ($fixture instanceof Role) {
                $fixture->setUuid($this->uuidService->create());

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
        $url = sprintf($url, $this->fixtures['role_1']->getUuid());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfRoleIsNotInDB($url)
    {
        $url = sprintf($url, $this->uuidService->create());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideRole
     */
    public function testCreateLanguage($role)
    {
        if (isset($role['role[translatable][0][language]'])) {
            $role['role[translatable][0][language]'] = $this->fixtures['language_1']->getUuid();
        }

        $crawler = $this->client->request('POST', '/role/create/');
        $form = $crawler->selectButton('role[save]')->form($role);
        $this->client->submit($form);
        $this->assertResponseRedirects('/role/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Rôle créé avec succès.');
    }

    public function provideUrls()
    {
        yield ['/role/'];
        yield ['/role/%s/'];
        yield ['/role/create/'];
        yield ['/role/%s/update/'];
        yield ['/role/%s/delete/'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/role/%s/'];
        yield ['/role/%s/update/'];
        yield ['/role/%s/delete/'];
    }

    public function provideRole()
    {
        yield [
            [
                'role[name]' => 'role_test',
            ],
        ];
//        yield [
//            [
//                'role[name]' => 'role_test',
//                'role[translatable][0][message]' => 'message test',
//                'role[translatable][0][language]' => '',
//            ],
//        ];
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
