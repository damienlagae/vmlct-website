<?php

declare(strict_types=1);

namespace App\Shared\Seo\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Serves /robots.txt dynamically so the `Sitemap:` directive always
 * points at the current host (works in dev, staging and prod without
 * a static file to keep in sync).
 */
#[Route('/robots.txt', name: 'robots', methods: ['GET'])]
final class RobotsController extends AbstractController
{
    public function __invoke(UrlGeneratorInterface $router): Response
    {
        $sitemap = $router->generate('sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /_components/\nDisallow: /login\n\nSitemap: ".$sitemap."\n";

        $response = new Response($body);
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->setPublic();
        $response->setMaxAge(86400);

        return $response;
    }
}
