<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Controller;

use App\Module\Uitslagen\Repository\ResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/uitslagen', name: 'uitslagen_index', methods: ['GET'])]
final class UitslagenIndexController extends AbstractController
{
    public function __invoke(ResultRepository $repository): Response
    {
        return $this->render('@Uitslagen/index.html.twig', [
            'results' => $repository->findRecent(50),
        ]);
    }
}
