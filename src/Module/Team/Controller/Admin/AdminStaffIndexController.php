<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Repository\StaffRepository;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/staff', name: 'admin_staff_index', methods: ['GET'])]
#[IsGranted(TeamPermissions::view->value)]
final class AdminStaffIndexController extends AbstractAdminController
{
    public function __invoke(StaffRepository $repository): Response
    {
        return $this->render('@Team/admin/staff/index.html.twig', [
            'staff' => $repository->findBy([], ['role' => 'ASC', 'lastName' => 'ASC']),
        ]);
    }
}
