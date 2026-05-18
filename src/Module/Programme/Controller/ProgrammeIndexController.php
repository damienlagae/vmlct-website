<?php

declare(strict_types=1);

namespace App\Module\Programme\Controller;

use App\Module\Programme\Repository\RaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/programma', name: 'programme_index', methods: ['GET'])]
final class ProgrammeIndexController extends AbstractController
{
    public function __invoke(RaceRepository $repository): Response
    {
        $upcoming = $repository->findUpcoming();
        $past = $repository->findPast(20);

        return $this->render('@Programme/index.html.twig', [
            'upcoming' => $upcoming,
            'past' => $past,
            'upcomingByMonth' => RaceRepository::groupByMonth($upcoming),
            'pastByMonth' => RaceRepository::groupByMonth($past),
        ]);
    }
}
