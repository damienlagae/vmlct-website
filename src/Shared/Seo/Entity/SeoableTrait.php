<?php

declare(strict_types=1);

namespace App\Shared\Seo\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Adds `metaTitle` and `metaDescription` columns to any Doctrine entity
 * so it can override the `<title>` and `<meta description>` tags on the
 * public side independently from its display title.
 *
 * Combine with `SeoableInterface`.
 */
trait SeoableTrait
{
    #[ORM\Column(length: 200, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $metaDescription = null;

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
