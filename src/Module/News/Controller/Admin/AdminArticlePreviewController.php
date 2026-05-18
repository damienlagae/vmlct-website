<?php

declare(strict_types=1);

namespace App\Module\News\Controller\Admin;

use App\Module\News\Entity\Article;
use App\Module\News\Security\NewsPermissions;
use App\Shared\Content\BlockSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Renders the public Article template against the current DB state, with
 * no `isPublished` filter, so editors can preview drafts and scheduled
 * articles inside the admin preview pane (iframe).
 */
#[Route('/admin/news/{id}/preview', name: 'admin_article_preview', methods: ['GET'])]
#[IsGranted(NewsPermissions::edit->value, subject: 'article')]
final class AdminArticlePreviewController extends AbstractController
{
    public function __invoke(Article $article, BlockSerializer $serializer): Response
    {
        $response = $this->render('@News/show.html.twig', [
            'article' => $article,
            'blocks' => $serializer->deserialize($article->getContent()),
        ]);

        // Prevent caching so refreshes always pick up the latest save.
        $response->headers->set('Cache-Control', 'no-store, max-age=0');
        // Allow the preview to be embedded only by the same origin (admin).
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
