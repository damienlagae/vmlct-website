<?php

declare(strict_types=1);

namespace App\Tests\Shared\Seo\Controller;

use App\Module\News\Factory\ArticleFactory;
use App\Page\Factory\PageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class SitemapControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testSitemapEmitsXmlWithStaticRoutes(): void
    {
        $client = self::createClient();
        $client->request('GET', '/sitemap.xml');

        self::assertResponseIsSuccessful();
        self::assertStringStartsWith('application/xml', (string) $client->getResponse()->headers->get('Content-Type'));

        $xml = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('<urlset', $xml);
        // static routes
        self::assertStringContainsString('/nieuws</loc>', $xml);
        self::assertStringContainsString('/ploeg</loc>', $xml);
    }

    public function testSitemapIncludesPublishedArticlesAndPages(): void
    {
        $client = self::createClient();

        ArticleFactory::createOne([
            'title' => 'Live article',
            'slug' => 'live-article',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
        ]);
        ArticleFactory::new()->draft()->create(['slug' => 'hidden-draft']);

        PageFactory::createOne([
            'title' => 'Live page',
            'path' => 'live-page',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
        ]);
        PageFactory::new()->draft()->create(['path' => 'hidden-page']);

        $client->request('GET', '/sitemap.xml');
        $xml = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('/nieuws/live-article</loc>', $xml);
        self::assertStringContainsString('/live-page</loc>', $xml);
        // drafts must NOT be exposed in the sitemap
        self::assertStringNotContainsString('hidden-draft', $xml);
        self::assertStringNotContainsString('hidden-page', $xml);
    }
}
