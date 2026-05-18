<?php

declare(strict_types=1);

namespace App\Module\Menu\Controller\Admin;

use App\Module\Menu\Entity\MenuItem;
use App\Module\Menu\Form\MenuItemType;
use App\Module\Menu\Security\MenuPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/menu/new', name: 'admin_menu_create', methods: ['GET', 'POST'])]
#[IsGranted(MenuPermissions::create->value)]
final class AdminMenuCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MenuItemType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            \assert($item instanceof MenuItem);

            $em->persist($item);
            $em->flush();

            $this->addFlash('success', 'menu.flash.created');

            return $this->redirectToRoute('admin_menu_index');
        }

        return $this->render('@Menu/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
