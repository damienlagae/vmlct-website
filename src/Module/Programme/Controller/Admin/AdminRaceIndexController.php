<?php

declare(strict_types=1);

namespace App\Module\Programme\Controller\Admin;

use App\Module\Programme\Repository\RaceRepository;
use App\Module\Programme\Security\ProgrammePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/programme', name: 'admin_race_index', methods: ['GET'])]
#[IsGranted(ProgrammePermissions::view->value)]
final class AdminRaceIndexController extends AbstractAdminController
{
    public function __invoke(RaceRepository $repository): Response
    {
        return $this->render('@Programme/admin/index.html.twig', [
            'upcoming' => $repository->findUpcoming(),
            'past' => $repository->findPast(50),
        ]);
    }
}
