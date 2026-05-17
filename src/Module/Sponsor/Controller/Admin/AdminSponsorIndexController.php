<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Controller\Admin;

use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sponsors', name: 'admin_sponsor_index')]
final class AdminSponsorIndexController extends AbstractAdminController
{
    public function __invoke(): Response
    {
        return $this->render('@Shared/admin/coming-soon.html.twig', [
            'pageTitle' => 'Sponsors',
        ]);
    }
}
