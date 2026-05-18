<?php

declare(strict_types=1);

namespace App\Shared\Seo\Sitemap;

/**
 * Single URL entry for the sitemap XML output. Created by providers
 * and rendered as-is by the SitemapController template.
 */
final readonly class SitemapUrl
{
    public function __construct(
        public string $loc,
        public ?\DateTimeImmutable $lastmod = null,
        public ?string $changefreq = null,
        public ?float $priority = null,
    ) {
    }
}
