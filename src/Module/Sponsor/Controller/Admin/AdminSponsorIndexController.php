<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Controller\Admin;

use App\Module\Sponsor\Entity\Sponsor;
use App\Module\Sponsor\Repository\SponsorRepository;
use App\Module\Sponsor\Security\SponsorPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sponsors', name: 'admin_sponsor_index', methods: ['GET'])]
#[IsGranted(SponsorPermissions::view->value)]
final class AdminSponsorIndexController extends AbstractAdminController
{
    public function __invoke(SponsorRepository $repository): Response
    {
        $sponsors = $repository->findBy([], ['displayOrder' => 'ASC', 'name' => 'ASC']);
        $active = array_filter($sponsors, static fn (Sponsor $s): bool => $s->isActive());

        return $this->render('@Sponsor/admin/index.html.twig', [
            'sponsors' => $sponsors,
            'stats' => [
                'total' => \count($sponsors),
                'active' => \count($active),
                'inactive' => \count($sponsors) - \count($active),
            ],
        ]);
    }
}
