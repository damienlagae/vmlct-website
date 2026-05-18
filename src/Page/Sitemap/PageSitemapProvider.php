<?php

declare(strict_types=1);

namespace App\Page\Sitemap;

use App\Page\Repository\PageRepository;
use App\Shared\Seo\Sitemap\SitemapProviderInterface;
use App\Shared\Seo\Sitemap\SitemapUrl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PageSitemapProvider implements SitemapProviderInterface
{
    public function __construct(
        private readonly PageRepository $repository,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    public function urls(): iterable
    {
        $now = new \DateTimeImmutable();
        $qb = $this->repository->createQueryBuilder('p')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('now', $now)
            ->orderBy('p.title', 'ASC')
        ;

        foreach ($qb->getQuery()->toIterable() as $page) {
            yield new SitemapUrl(
                loc: $this->router->generate('page_show', ['path' => $page->getPath()], UrlGeneratorInterface::ABSOLUTE_URL),
                lastmod: $page->getUpdatedAt() ?? $page->getPublishedAt(),
                changefreq: 'monthly',
                priority: 0.7,
            );
        }
    }
}
