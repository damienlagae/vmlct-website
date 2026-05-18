<?php

declare(strict_types=1);

namespace App\Shared\Seo\Entity;

/**
 * Contract implemented by every entity that exposes per-record SEO
 * overrides. Consumers (Twig SEO component, sitemap providers) only
 * see this interface — never the concrete entity.
 */
interface SeoableInterface
{
    public function getMetaTitle(): ?string;

    public function getMetaDescription(): ?string;
}
