<?php

declare(strict_types=1);

namespace App\Module\News\Controller;

use App\Module\News\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nieuws/{slug}', name: 'news_show', methods: ['GET'])]
final class NewsShowController extends AbstractController
{
    public function __invoke(string $slug, ArticleRepository $repository): Response
    {
        $article = $repository->findOnePublishedBySlug($slug);
        if (null === $article) {
            throw new NotFoundHttpException();
        }

        return $this->render('@News/show.html.twig', [
            'article' => $article,
        ]);
    }
}
