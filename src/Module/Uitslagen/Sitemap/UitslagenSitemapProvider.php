<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Sitemap;

use App\Shared\Seo\Sitemap\SitemapProviderInterface;
use App\Shared\Seo\Sitemap\SitemapUrl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UitslagenSitemapProvider implements SitemapProviderInterface
{
    public function __construct(private readonly UrlGeneratorInterface $router)
    {
    }

    public function urls(): iterable
    {
        yield new SitemapUrl(
            loc: $this->router->generate('uitslagen_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
            changefreq: 'weekly',
            priority: 0.6,
        );
    }
}
