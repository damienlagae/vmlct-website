<?php

declare(strict_types=1);

namespace App\Tests\Module\News;

use App\Module\News\Factory\ArticleFactory;
use App\Shared\Content\Block\HeadingBlock;
use App\Shared\Content\Block\HeadingLevel;
use App\Shared\Content\Block\ImageAlignment;
use App\Shared\Content\Block\ImageBlock;
use App\Shared\Content\Block\TextBlock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class NewsShowBlocksTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testShowRendersTheThreeInitialBlockTypes(): void
    {
        $client = self::createClient();

        ArticleFactory::createOne([
            'title' => 'Article with blocks',
            'slug' => 'article-with-blocks',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
            'content' => [
                (new TextBlock('<p>First paragraph with <strong>bold</strong>.</p>'))->toArray(),
                (new HeadingBlock('A subtitle', HeadingLevel::H3))->toArray(),
                (new ImageBlock('sample.jpg', 'Sample image', 'A caption', ImageAlignment::Center))->toArray(),
            ],
        ]);

        $client->request('GET', '/nieuws/article-with-blocks');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('content-block--text', $html);
        self::assertStringContainsString('<strong>bold</strong>', $html);

        self::assertStringContainsString('content-block--heading', $html);
        self::assertStringContainsString('<h3', $html);
        self::assertStringContainsString('A subtitle', $html);

        self::assertStringContainsString('content-block--image', $html);
        self::assertStringContainsString('content-block--image-center', $html);
        self::assertStringContainsString('alt="Sample image"', $html);
        self::assertStringContainsString('A caption', $html);
    }
}
