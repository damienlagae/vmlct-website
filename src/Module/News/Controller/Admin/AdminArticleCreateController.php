<?php

declare(strict_types=1);

namespace App\Module\News\Controller\Admin;

use App\Module\News\Entity\Article;
use App\Module\News\Form\ArticleType;
use App\Module\News\Security\NewsPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Security\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/news/new', name: 'admin_article_create', methods: ['GET', 'POST'])]
#[IsGranted(NewsPermissions::create->value)]
final class AdminArticleCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            \assert($article instanceof Article);

            if ('' === $article->getSlug()) {
                $article->setSlug($slugger->slug($article->getTitle())->lower()->toString());
            }

            $user = $this->getUser();
            if ($user instanceof User) {
                $article->setAuthor($user);
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'news.flash.created');

            return $this->redirectToRoute('admin_article_edit', ['id' => (string) $article->getId()]);
        }

        return $this->render('@News/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
