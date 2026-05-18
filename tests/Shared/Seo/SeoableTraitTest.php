<?php

declare(strict_types=1);

namespace App\Tests\Shared\Seo;

use App\Module\News\Entity\Article;
use App\Page\Entity\Page;
use App\Shared\Seo\Entity\SeoableInterface;
use PHPUnit\Framework\TestCase;

final class SeoableTraitTest extends TestCase
{
    public function testArticleImplementsSeoableInterface(): void
    {
        $article = new Article('title', 'slug');

        self::assertInstanceOf(SeoableInterface::class, $article);
        self::assertNull($article->getMetaTitle());
        self::assertNull($article->getMetaDescription());

        $article->setMetaTitle('OG title');
        $article->setMetaDescription('OG description');

        self::assertSame('OG title', $article->getMetaTitle());
        self::assertSame('OG description', $article->getMetaDescription());
    }

    public function testPageImplementsSeoableInterface(): void
    {
        $page = new Page('About', 'about');

        self::assertInstanceOf(SeoableInterface::class, $page);
        self::assertNull($page->getMetaTitle());

        $page->setMetaTitle('Custom');
        self::assertSame('Custom', $page->getMetaTitle());
    }
}
