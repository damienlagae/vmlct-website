<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Repository\RiderRepository;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/renners', name: 'admin_rider_index', methods: ['GET'])]
#[IsGranted(TeamPermissions::view->value)]
final class AdminRiderIndexController extends AbstractAdminController
{
    public function __invoke(RiderRepository $repository): Response
    {
        return $this->render('@Team/admin/rider/index.html.twig', [
            'riders' => $repository->findBy([], ['category' => 'ASC', 'lastName' => 'ASC']),
        ]);
    }
}
