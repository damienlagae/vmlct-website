<?php

declare(strict_types=1);

namespace App\Tests\Page\Controller\Admin;

use App\Page\Factory\PageFactory;
use App\Page\Repository\PageRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminPageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexRedirectsAnonymousToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/pages');

        self::assertResponseRedirects('/login');
    }

    public function testIndexForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());
        $client->request('GET', '/admin/pages');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexListsPagesAndStats(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        PageFactory::createOne(['title' => 'Over', 'path' => 'over', 'publishedAt' => new \DateTimeImmutable('-1 day')]);
        PageFactory::new()->draft()->create(['title' => 'Brouillon', 'path' => 'brouillon']);

        $client->request('GET', '/admin/pages');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Over', $html);
        self::assertStringContainsString('Brouillon', $html);
        self::assertStringContainsString('/over', $html);
    }

    public function testCreatePersistsAPage(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', '/admin/pages/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Opslaan')->form();
        $form['page[title]']->setValue('Nieuwe pagina');
        $form['page[path]']->setValue('nieuwe-pagina');
        $form['page[excerpt]']->setValue('Een nieuwe pagina');
        $form['page[content]']->setValue('[]');

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $repo = static::getContainer()->get(PageRepository::class);
        $stored = $repo->findOneBy(['path' => 'nieuwe-pagina']);
        self::assertNotNull($stored);
        self::assertSame('Nieuwe pagina', $stored->getTitle());
    }

    public function testEditPersistsBlocksFromHiddenJsonField(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $page = PageFactory::createOne([
            'title' => 'Initial',
            'path' => 'initial',
            'content' => [],
        ]);

        $crawler = $client->request('GET', '/admin/pages/'.$page->getId().'/edit');

        $payload = [
            ['id' => '01HKR000000000000000000A01', 'type' => 'text', 'html' => '<p>Inhoud van de pagina.</p>'],
            ['id' => '01HKR000000000000000000A02', 'type' => 'heading', 'text' => 'Sectie', 'level' => 'h2'],
        ];

        $form = $crawler->selectButton('Opslaan')->form();
        $form['page[title]']->setValue('Bijgewerkt');
        $form['page[content]']->setValue((string) json_encode($payload, \JSON_THROW_ON_ERROR));

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $repo = static::getContainer()->get(PageRepository::class);
        $fresh = $repo->find($page->getId());
        self::assertNotNull($fresh);
        self::assertSame('Bijgewerkt', $fresh->getTitle());

        $stored = $fresh->getContent();
        self::assertCount(2, $stored);
        self::assertSame('text', $stored[0]['type']);
        self::assertSame('heading', $stored[1]['type']);
    }

    public function testPathValidationRejectsInvalidCharacters(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', '/admin/pages/new');
        $form = $crawler->selectButton('Opslaan')->form();
        $form['page[title]']->setValue('Bad');
        $form['page[path]']->setValue('/bad path WITH spaces/');
        $form['page[content]']->setValue('[]');

        $client->submit($form);

        // invalid form re-renders with 422 Unprocessable Content
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $repo = static::getContainer()->get(PageRepository::class);
        self::assertNull($repo->findOneBy(['title' => 'Bad']));
    }

    public function testDeleteRemovesPageWhenCsrfValid(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->superAdmin()->create());

        $page = PageFactory::createOne(['path' => 'to-delete']);
        $id = $page->getId();

        // Fetch the index so a CSRF token tied to this session is rendered.
        $client->request('GET', '/admin/pages');
        $form = $client->getCrawler()
            ->filter('form[action="/admin/pages/'.$id.'/delete"]')
            ->form()
        ;
        $client->submit($form);

        self::assertResponseRedirects('/admin/pages');
        $repo = static::getContainer()->get(PageRepository::class);
        self::assertNull($repo->find($id));
    }
}
