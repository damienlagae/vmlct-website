<?php

declare(strict_types=1);

namespace App\Shared\Seo\Twig\Component;

use App\Shared\Seo\Entity\SeoableInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Resolves the SEO meta values for a public page. Templates pass either
 * a Seoable entity (Article / Page) or override explicitly with `title`
 * / `description` props. Falls back to the app-wide defaults
 * (`app.title` / `app.description` translation keys, rendered by the
 * template).
 *
 * Usage:
 *   <twig:Site:SeoMeta :entity="article" />            {# entity-driven #}
 *   <twig:Site:SeoMeta title="..." description="..." />{# explicit override #}
 */
#[AsTwigComponent(name: 'Site:SeoMeta', template: '@Shared/seo/SeoMeta.html.twig')]
final class SeoMeta
{
    public ?SeoableInterface $entity = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $image = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[ExposeInTemplate('resolvedTitle')]
    public function resolveTitle(): ?string
    {
        if (null !== $this->title && '' !== $this->title) {
            return $this->title;
        }

        $entity = $this->entity;
        if ($entity instanceof SeoableInterface) {
            $meta = $entity->getMetaTitle();
            if (null !== $meta && '' !== $meta) {
                return $meta;
            }
        }

        return null;
    }

    #[ExposeInTemplate('resolvedDescription')]
    public function resolveDescription(): ?string
    {
        if (null !== $this->description && '' !== $this->description) {
            return $this->description;
        }

        $entity = $this->entity;
        if ($entity instanceof SeoableInterface) {
            $meta = $entity->getMetaDescription();
            if (null !== $meta && '' !== $meta) {
                return $meta;
            }
        }

        return null;
    }

    #[ExposeInTemplate('canonicalUrl')]
    public function canonical(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return '';
        }

        return $request->getSchemeAndHttpHost().$request->getPathInfo();
    }
}
