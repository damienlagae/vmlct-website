<?php

declare(strict_types=1);

namespace App\Module\Programme\Sitemap;

use App\Shared\Seo\Sitemap\SitemapProviderInterface;
use App\Shared\Seo\Sitemap\SitemapUrl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The /programma index is itself a route the static provider already
 * covers, but listing it here keeps the module owning its own sitemap
 * entry so it stays self-contained when extracted. Individual Race rows
 * have no dedicated public page yet — when they do, yield them here.
 */
final class ProgrammeSitemapProvider implements SitemapProviderInterface
{
    public function __construct(private readonly UrlGeneratorInterface $router)
    {
    }

    public function urls(): iterable
    {
        yield new SitemapUrl(
            loc: $this->router->generate('programme_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
            changefreq: 'weekly',
            priority: 0.7,
        );
    }
}
