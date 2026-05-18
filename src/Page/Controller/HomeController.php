<?php

declare(strict_types=1);

namespace App\Page\Controller;

use App\Module\Programme\Repository\RaceRepository;
use App\Module\Uitslagen\Repository\ResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'home')]
final class HomeController extends AbstractController
{
    public function __invoke(RaceRepository $races, ResultRepository $results): Response
    {
        return $this->render('@Page/home/index.html.twig', [
            'nextRaces' => \array_slice($races->findUpcoming(), 0, 3),
            'recentResults' => $results->findRecent(6),
        ]);
    }
}
