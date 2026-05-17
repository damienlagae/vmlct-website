<?php

declare(strict_types=1);

namespace App\Tests\Module\News\Controller\Admin;

use App\Module\News\Factory\ArticleFactory;
use App\Module\News\Repository\ArticleRepository;
use App\Shared\Content\Block\HeadingLevel;
use App\Shared\Content\Block\ImageAlignment;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminArticleEditorTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testEditPageRendersTheLiveContentEditor(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $article = ArticleFactory::createOne([
            'title' => 'Editor demo',
            'slug' => 'editor-demo',
            'content' => [
                ['type' => 'text', 'html' => '<p>existing</p>'],
                ['type' => 'heading', 'text' => 'Subtitle', 'level' => HeadingLevel::H3->value],
            ],
        ]);

        $client->loginUser($admin);
        $client->request('GET', '/admin/news/'.$article->getId().'/edit');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('content-editor', $html);
        self::assertStringContainsString('data-testid="content-editor-list"', $html);
        self::assertStringContainsString('data-block-type="text"', $html);
        self::assertStringContainsString('data-block-type="heading"', $html);
        self::assertStringContainsString('name="article[content]"', $html);
    }

    public function testSubmittingTheFormPersistsTheBlocksFromTheHiddenJsonField(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $article = ArticleFactory::createOne([
            'title' => 'Initial title',
            'slug' => 'initial-slug',
            'content' => [],
        ]);

        $client->loginUser($admin);
        $crawler = $client->request('GET', '/admin/news/'.$article->getId().'/edit');

        $payload = [
            ['id' => '01HKR000000000000000000001', 'type' => 'text', 'html' => '<p>From form submit</p>'],
            ['id' => '01HKR000000000000000000002', 'type' => 'image', 'path' => 'submitted.jpg', 'alt' => 'alt', 'caption' => 'cap', 'alignment' => ImageAlignment::Full->value],
        ];

        $form = $crawler->selectButton('Opslaan')->form();
        $form['article[title]']->setValue('Updated title');
        $form['article[content]']->setValue((string) json_encode($payload, \JSON_THROW_ON_ERROR));

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $repo = static::getContainer()->get(ArticleRepository::class);
        $fresh = $repo->find($article->getId());
        self::assertNotNull($fresh);
        self::assertSame('Updated title', $fresh->getTitle());

        $stored = $fresh->getContent();
        self::assertCount(2, $stored);
        self::assertSame('01HKR000000000000000000001', $stored[0]['id']);
        self::assertSame(0, $stored[0]['position']);
        self::assertSame('text', $stored[0]['type']);
        self::assertSame('<p>From form submit</p>', $stored[0]['html']);
        self::assertSame('01HKR000000000000000000002', $stored[1]['id']);
        self::assertSame(1, $stored[1]['position']);
        self::assertSame('image', $stored[1]['type']);
        self::assertSame('submitted.jpg', $stored[1]['path']);
    }

    public function testSetContentNormalisesLegacyListShapeOnNextSave(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $article = ArticleFactory::createOne([
            'title' => 'Legacy article',
            'slug' => 'legacy-article',
            'content' => [
                ['type' => 'text', 'html' => '<p>legacy</p>'],
                ['type' => 'heading', 'text' => 'Old subtitle', 'level' => HeadingLevel::H3->value],
            ],
        ]);

        $stored = $article->getContent();
        self::assertCount(2, $stored);
        // setContent (called by the Foundry factory) has normalised legacy input:
        // each entry has an id (ULID assigned because none was provided) and a position.
        self::assertNotEmpty($stored[0]['id']);
        self::assertNotEmpty($stored[1]['id']);
        self::assertNotSame($stored[0]['id'], $stored[1]['id']);
        self::assertSame(0, $stored[0]['position']);
        self::assertSame(1, $stored[1]['position']);

        $client->loginUser($admin);
    }
}
