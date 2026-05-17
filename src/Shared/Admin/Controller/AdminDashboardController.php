<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_dashboard')]
final class AdminDashboardController extends AbstractAdminController
{
    public function __invoke(): Response
    {
        return $this->render('@Shared/admin/dashboard.html.twig');
    }
}
