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

#[Route('/admin/pages/new', name: 'admin_page_create', methods: ['GET', 'POST'])]
#[IsGranted(PagePermissions::create->value)]
final class AdminPageCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page = $form->getData();
            \assert($page instanceof Page);

            $em->persist($page);
            $em->flush();

            $this->addFlash('success', 'page.flash.created');

            return $this->redirectToRoute('admin_page_edit', ['id' => (string) $page->getId()]);
        }

        return $this->render('@Page/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
