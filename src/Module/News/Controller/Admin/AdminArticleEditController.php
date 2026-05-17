<?php

declare(strict_types=1);

namespace App\Module\News\Controller\Admin;

use App\Module\News\Entity\Article;
use App\Module\News\Form\ArticleType;
use App\Module\News\Security\NewsPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/news/{id}/edit', name: 'admin_article_edit', methods: ['GET', 'POST'])]
#[IsGranted(NewsPermissions::edit->value, subject: 'article')]
final class AdminArticleEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Article $article, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ('' === $article->getSlug()) {
                $article->setSlug($slugger->slug($article->getTitle())->lower()->toString());
            }

            $em->flush();

            $this->addFlash('success', 'news.flash.updated');

            return $this->redirectToRoute('admin_article_edit', ['id' => (string) $article->getId()]);
        }

        return $this->render('@News/admin/edit.html.twig', [
            'form' => $form,
            'article' => $article,
        ]);
    }
}
