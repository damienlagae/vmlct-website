<?php

declare(strict_types=1);

namespace App\Module\Menu\Controller\Admin;

use App\Module\Menu\Repository\MenuItemRepository;
use App\Module\Menu\Security\MenuPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/menu', name: 'admin_menu_index', methods: ['GET'])]
#[IsGranted(MenuPermissions::view->value)]
final class AdminMenuIndexController extends AbstractAdminController
{
    public function __invoke(MenuItemRepository $repository): Response
    {
        return $this->render('@Menu/admin/index.html.twig', [
            'roots' => $repository->findBy(['parent' => null], ['position' => 'ASC']),
        ]);
    }
}
