<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller\User;

use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Security\Entity\User;
use App\Shared\Security\Permissions\UserPermissions;
use App\Shared\Security\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'admin_user_index', methods: ['GET'])]
#[IsGranted(UserPermissions::view->value)]
final class AdminUserIndexController extends AbstractAdminController
{
    public function __invoke(UserRepository $repository): Response
    {
        $users = $repository->findBy([], ['email' => 'ASC']);
        $active = array_filter($users, static fn (User $u): bool => $u->isActive());
        $admins = array_filter($users, static fn (User $u): bool => \in_array('ROLE_ADMIN', $u->getRoles(), true) || \in_array('ROLE_SUPER_ADMIN', $u->getRoles(), true));

        return $this->render('@Shared/admin/user/index.html.twig', [
            'users' => $users,
            'stats' => [
                'total' => \count($users),
                'active' => \count($active),
                'admins' => \count($admins),
            ],
        ]);
    }
}
