<?php

declare(strict_types=1);

namespace App\Module\News\Controller;

use App\Module\News\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nieuws', name: 'news_index', methods: ['GET'])]
final class NewsIndexController extends AbstractController
{
    private const PER_PAGE = 12;

    public function __invoke(Request $request, ArticleRepository $repository): Response
    {
        $page = max(1, (int) $request->query->get('page', '1'));
        $paginator = $repository->findPublishedPaginated($page, self::PER_PAGE);

        $total = \count($paginator);
        $pages = (int) ceil($total / self::PER_PAGE);

        return $this->render('@News/index.html.twig', [
            'articles' => $paginator,
            'currentPage' => $page,
            'totalPages' => max(1, $pages),
            'total' => $total,
        ]);
    }
}
