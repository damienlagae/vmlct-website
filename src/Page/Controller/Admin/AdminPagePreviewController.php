<?php

declare(strict_types=1);

namespace App\Page\Controller\Admin;

use App\Page\Entity\Page;
use App\Page\Security\PagePermissions;
use App\Shared\Content\BlockSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Renders the public Page template against the current DB state, with no
 * `isPublished` filter, so editors can preview drafts inside the admin
 * preview pane (iframe).
 */
#[Route('/admin/pages/{id}/preview', name: 'admin_page_preview', methods: ['GET'])]
#[IsGranted(PagePermissions::edit->value, subject: 'page')]
final class AdminPagePreviewController extends AbstractController
{
    public function __invoke(Page $page, BlockSerializer $serializer): Response
    {
        $response = $this->render('@Page/show.html.twig', [
            'page' => $page,
            'blocks' => $serializer->deserialize($page->getContent()),
        ]);

        $response->headers->set('Cache-Control', 'no-store, max-age=0');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
