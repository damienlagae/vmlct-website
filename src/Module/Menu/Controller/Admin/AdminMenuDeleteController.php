<?php

declare(strict_types=1);

namespace App\Module\Menu\Controller\Admin;

use App\Module\Menu\Entity\MenuItem;
use App\Module\Menu\Security\MenuPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/menu/{id}/delete', name: 'admin_menu_delete', methods: ['POST'])]
#[IsGranted(MenuPermissions::delete->value, subject: 'item')]
final class AdminMenuDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, MenuItem $item, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-menu-'.$item->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($item);
        $em->flush();

        $this->addFlash('success', 'menu.flash.deleted');

        return $this->redirectToRoute('admin_menu_index');
    }
}
