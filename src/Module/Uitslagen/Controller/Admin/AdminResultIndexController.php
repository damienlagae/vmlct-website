<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Controller\Admin;

use App\Module\Uitslagen\Repository\ResultRepository;
use App\Module\Uitslagen\Security\UitslagenPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/uitslagen', name: 'admin_result_index', methods: ['GET'])]
#[IsGranted(UitslagenPermissions::view->value)]
final class AdminResultIndexController extends AbstractAdminController
{
    public function __invoke(ResultRepository $repository): Response
    {
        return $this->render('@Uitslagen/admin/index.html.twig', [
            'results' => $repository->findRecent(200),
        ]);
    }
}
