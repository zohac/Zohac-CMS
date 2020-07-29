<?php

namespace App\Tests\Controller;

use App\Entity\Language;
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
     * @var Language[]
     */
    private $languages;

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

        $this->loadLanguages();
    }

    public function loadLanguages()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $this->languages = $this->loadFixtureFiles([
            __DIR__.'/../DataFixtures/LanguageFixtures.yaml',
        ]);

        foreach ($this->languages as $key => $language) {
            $language->setUuid($this->uuidService->create());
            $entityManager->persist($language);

            $this->languages[$key] = $language;
        }
        $entityManager->flush();
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($url)
    {
        $url = sprintf($url, $this->languages['language1']->getUuid());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideUrlsForRedirection
     */
    public function testPageIsRedirectedIfLanguageIsNotInDB($url)
    {
        $url = sprintf($url, $this->uuidService->create());
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideBadLanguageCredentials
     *
     * @param $badCredential array
     */
    public function testCreateLanguageWithBadCredential($badCredential)
    {
        $crawler = $this->client->request('POST', '/language/create/');
        $form = $crawler->selectButton('language[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testCreateLanguage()
    {
        $crawler = $this->client->request('POST', '/language/create/');
        $form = $crawler->selectButton('language[save]')->form([
            'language[name]' => 'test',
            'language[alternateName]' => 'Test',
            'language[description]' => 'a language test',
            'language[iso6391]' => 'ts',
            'language[iso6392T]' => 'tes',
            'language[iso6392B]' => 'tes',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue créée avec succès.');
    }

    /**
     * @dataProvider provideBadLanguageCredentials
     *
     * @param $badCredential array
     */
    public function testUpdateLanguageWithBadCredentials($badCredential)
    {
        $uri = sprintf('/language/%s/update/', $this->languages['language1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('language[save]')->form($badCredential);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.form-error-wrapper');
    }

    public function testUpdateLanguage()
    {
        $uri = sprintf('/language/%s/update/', $this->languages['language1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('language[save]')->form([
            'language[name]' => 'test_update',
            'language[alternateName]' => 'Test_update',
            'language[description]' => 'a language test update',
            'language[iso6391]' => 'ts',
            'language[iso6392T]' => 'tes',
            'language[iso6392B]' => 'tes',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue mise à jour avec succès.');
    }

    public function testDeleteLanguage()
    {
        $uri = sprintf('/language/%s/delete/', $this->languages['language1']->getUuid());
        $crawler = $this->client->request('POST', $uri);
        $form = $crawler->selectButton('delete[delete]')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects('/language/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('div', 'Langue supprimée avec succès.');
    }

    public function provideUrls()
    {
        yield ['/language/'];
        yield ['/language/%s/'];
        yield ['/language/create/'];
        yield ['/language/%s/update/'];
        yield ['/language/%s/delete/'];
    }

    public function provideUrlsForRedirection()
    {
        yield ['/language/%s/'];
        yield ['/language/%s/update/'];
        yield ['/language/%s/delete/'];
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
        $this->languages = null;
        $this->uuidService = null;
    }
}
