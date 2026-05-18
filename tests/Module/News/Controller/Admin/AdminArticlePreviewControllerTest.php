<?php

declare(strict_types=1);

namespace App\Tests\Module\News\Controller\Admin;

use App\Module\News\Factory\ArticleFactory;
use App\Shared\Content\Block\TextBlock;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminArticlePreviewControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPreviewRequiresAuth(): void
    {
        $client = self::createClient();
        $article = ArticleFactory::createOne();

        $client->request('GET', '/admin/news/'.$article->getId().'/preview');

        self::assertResponseRedirects('/login');
    }

    public function testPreviewRequiresEditPermission(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());
        $article = ArticleFactory::createOne();

        $client->request('GET', '/admin/news/'.$article->getId().'/preview');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testPreviewServesDraftArticles(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $article = ArticleFactory::new()->draft()->create([
            'title' => 'Unpublished draft',
            'slug' => 'unpublished-draft',
            'content' => [
                (new TextBlock('<p>Draft body visible to editor.</p>'))->toArray(),
            ],
        ]);

        $client->request('GET', '/admin/news/'.$article->getId().'/preview');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Unpublished draft', $html);
        self::assertStringContainsString('Draft body visible to editor', $html);
        self::assertStringContainsString('no-store', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertSame('SAMEORIGIN', $client->getResponse()->headers->get('X-Frame-Options'));
    }
}
