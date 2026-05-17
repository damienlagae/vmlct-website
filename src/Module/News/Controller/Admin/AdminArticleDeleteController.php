<?php

declare(strict_types=1);

namespace App\Module\News\Controller\Admin;

use App\Module\News\Entity\Article;
use App\Module\News\Security\NewsPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/news/{id}/delete', name: 'admin_article_delete', methods: ['POST'])]
#[IsGranted(NewsPermissions::delete->value, subject: 'article')]
final class AdminArticleDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-article-'.$article->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'news.flash.deleted');

        return $this->redirectToRoute('admin_article_index');
    }
}
