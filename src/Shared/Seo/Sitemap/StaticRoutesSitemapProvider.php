<?php

declare(strict_types=1);

namespace App\Shared\Seo\Sitemap;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Emits the handful of stable, public, parameterless routes (home,
 * news index, team index). Module-specific lists are emitted by their
 * own providers (NewsSitemapProvider, PageSitemapProvider).
 */
final class StaticRoutesSitemapProvider implements SitemapProviderInterface
{
    private const STATIC_ROUTES = [
        'home' => ['changefreq' => 'weekly', 'priority' => 1.0],
        'news_index' => ['changefreq' => 'daily', 'priority' => 0.8],
        'team_index' => ['changefreq' => 'monthly', 'priority' => 0.7],
    ];

    public function __construct(private readonly UrlGeneratorInterface $router)
    {
    }

    public function urls(): iterable
    {
        foreach (self::STATIC_ROUTES as $name => $meta) {
            yield new SitemapUrl(
                loc: $this->router->generate($name, [], UrlGeneratorInterface::ABSOLUTE_URL),
                changefreq: $meta['changefreq'],
                priority: $meta['priority'],
            );
        }
    }
}
