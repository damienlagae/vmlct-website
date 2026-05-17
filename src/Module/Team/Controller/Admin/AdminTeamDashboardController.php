<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Repository\RiderRepository;
use App\Module\Team\Repository\StaffRepository;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team', name: 'admin_team_dashboard', methods: ['GET'])]
#[IsGranted(TeamPermissions::view->value)]
final class AdminTeamDashboardController extends AbstractAdminController
{
    public function __invoke(RiderRepository $riders, StaffRepository $staff): Response
    {
        return $this->render('@Team/admin/dashboard.html.twig', [
            'totalRiders' => $riders->count([]),
            'totalStaff' => $staff->count([]),
        ]);
    }
}
