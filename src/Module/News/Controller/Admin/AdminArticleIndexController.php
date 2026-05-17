<?php

declare(strict_types=1);

namespace App\Module\News\Controller\Admin;

use App\Module\News\Entity\Article;
use App\Module\News\Repository\ArticleRepository;
use App\Module\News\Security\NewsPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/news', name: 'admin_article_index', methods: ['GET'])]
#[IsGranted(NewsPermissions::view->value)]
final class AdminArticleIndexController extends AbstractAdminController
{
    public function __invoke(ArticleRepository $repository): Response
    {
        $articles = $repository->findBy([], ['publishedAt' => 'DESC', 'createdAt' => 'DESC']);
        $now = new \DateTimeImmutable();
        $published = array_filter($articles, static fn (Article $a): bool => null !== $a->getPublishedAt() && $a->getPublishedAt() <= $now);

        return $this->render('@News/admin/index.html.twig', [
            'articles' => $articles,
            'stats' => [
                'total' => \count($articles),
                'published' => \count($published),
                'draft' => \count($articles) - \count($published),
            ],
        ]);
    }
}
