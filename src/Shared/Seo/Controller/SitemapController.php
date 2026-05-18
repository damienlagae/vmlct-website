<?php

declare(strict_types=1);

namespace App\Shared\Seo\Controller;

use App\Shared\Seo\Sitemap\SitemapProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
final class SitemapController extends AbstractController
{
    /**
     * @param iterable<SitemapProviderInterface> $providers
     */
    public function __construct(
        #[AutowireIterator('app.sitemap_provider')]
        private readonly iterable $providers,
    ) {
    }

    public function __invoke(): Response
    {
        $urls = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->urls() as $url) {
                $urls[] = $url;
            }
        }

        $response = $this->render('@Shared/seo/sitemap.xml.twig', ['urls' => $urls]);
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');
        // Sitemaps don't change often enough to justify revalidation chatter.
        $response->setPublic();
        $response->setMaxAge(3600);

        return $response;
    }
}
