<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller\User;

use App\Shared\Admin\Controller\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users', name: 'admin_user_index')]
final class AdminUserIndexController extends AbstractAdminController
{
    public function __invoke(): Response
    {
        return $this->render('@Shared/admin/coming-soon.html.twig', [
            'pageTitle' => 'Gebruikers',
        ]);
    }
}
