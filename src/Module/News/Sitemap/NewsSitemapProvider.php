<?php

declare(strict_types=1);

namespace App\Module\News\Sitemap;

use App\Module\News\Repository\ArticleRepository;
use App\Shared\Seo\Sitemap\SitemapProviderInterface;
use App\Shared\Seo\Sitemap\SitemapUrl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class NewsSitemapProvider implements SitemapProviderInterface
{
    public function __construct(
        private readonly ArticleRepository $repository,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    public function urls(): iterable
    {
        $now = new \DateTimeImmutable();
        $qb = $this->repository->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('now', $now)
            ->orderBy('a.publishedAt', 'DESC')
        ;

        foreach ($qb->getQuery()->toIterable() as $article) {
            yield new SitemapUrl(
                loc: $this->router->generate('news_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                lastmod: $article->getUpdatedAt() ?? $article->getPublishedAt(),
                changefreq: 'monthly',
                priority: 0.6,
            );
        }
    }
}
