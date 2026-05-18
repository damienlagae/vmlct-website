<?php

declare(strict_types=1);

namespace App\Page\Controller\Admin;

use App\Page\Entity\Page;
use App\Page\Security\PagePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pages/{id}/delete', name: 'admin_page_delete', methods: ['POST'])]
#[IsGranted(PagePermissions::delete->value, subject: 'page')]
final class AdminPageDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Page $page, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-page-'.$page->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($page);
        $em->flush();

        $this->addFlash('success', 'page.flash.deleted');

        return $this->redirectToRoute('admin_page_index');
    }
}
