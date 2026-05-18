<?php

declare(strict_types=1);

namespace App\Page\Controller\Admin;

use App\Page\Entity\Page;
use App\Page\Repository\PageRepository;
use App\Page\Security\PagePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pages', name: 'admin_page_index', methods: ['GET'])]
#[IsGranted(PagePermissions::view->value)]
final class AdminPageIndexController extends AbstractAdminController
{
    public function __invoke(PageRepository $repository): Response
    {
        $pages = $repository->findBy([], ['title' => 'ASC']);
        $now = new \DateTimeImmutable();
        $published = array_filter($pages, static fn (Page $p): bool => null !== $p->getPublishedAt() && $p->getPublishedAt() <= $now);

        return $this->render('@Page/admin/index.html.twig', [
            'pages' => $pages,
            'stats' => [
                'total' => \count($pages),
                'published' => \count($published),
                'draft' => \count($pages) - \count($published),
            ],
        ]);
    }
}
