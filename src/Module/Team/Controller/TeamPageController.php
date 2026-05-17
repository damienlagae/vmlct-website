<?php

declare(strict_types=1);

namespace App\Module\Team\Controller;

use App\Module\Team\Entity\RiderCategory;
use App\Module\Team\Repository\RiderRepository;
use App\Module\Team\Repository\StaffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ploeg', name: 'team_index', methods: ['GET'])]
final class TeamPageController extends AbstractController
{
    public function __invoke(RiderRepository $riders, StaffRepository $staff): Response
    {
        $ridersByCategory = $riders->findActiveGroupedByCategory();
        $totalRiders = array_sum(array_map(static fn (array $group): int => \count($group), $ridersByCategory));

        return $this->render('@Team/index.html.twig', [
            'staff' => $staff->findActiveOrdered(),
            'ridersByCategory' => $ridersByCategory,
            'categories' => RiderCategory::ordered(),
            'totalRiders' => $totalRiders,
        ]);
    }
}
