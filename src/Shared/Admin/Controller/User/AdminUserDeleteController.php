<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller\User;

use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Security\Entity\User;
use App\Shared\Security\Permissions\UserPermissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
#[IsGranted(UserPermissions::delete->value, subject: 'user')]
final class AdminUserDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-user-'.$user->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        if ($this->getUser() === $user) {
            $this->addFlash('danger', 'user.flash.cannot_delete_self');

            return $this->redirectToRoute('admin_user_index');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'user.flash.deleted');

        return $this->redirectToRoute('admin_user_index');
    }
}
