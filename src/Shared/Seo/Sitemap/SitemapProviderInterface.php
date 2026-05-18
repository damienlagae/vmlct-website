<?php

declare(strict_types=1);

namespace App\Shared\Seo\Sitemap;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Implemented by each module that contributes URLs to /sitemap.xml.
 *
 * Auto-tagged via `_instanceof` so adding a new provider is a matter
 * of creating a class that implements this interface; the controller
 * picks it up automatically through the `app.sitemap_provider` tag.
 */
#[AutoconfigureTag('app.sitemap_provider')]
interface SitemapProviderInterface
{
    /**
     * @return iterable<SitemapUrl>
     */
    public function urls(): iterable;
}
