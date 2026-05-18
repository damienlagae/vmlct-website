<?php

declare(strict_types=1);

namespace App\Page\Controller\Admin;

use App\Page\Entity\Page;
use App\Page\Form\PageType;
use App\Page\Security\PagePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pages/{id}/edit', name: 'admin_page_edit', methods: ['GET', 'POST'])]
#[IsGranted(PagePermissions::edit->value, subject: 'page')]
final class AdminPageEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Page $page, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'page.flash.updated');

            return $this->redirectToRoute('admin_page_edit', ['id' => (string) $page->getId()]);
        }

        return $this->render('@Page/admin/edit.html.twig', [
            'form' => $form,
            'page' => $page,
        ]);
    }
}
