<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller\User;

use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Security\Entity\User;
use App\Shared\Security\Form\UserType;
use App\Shared\Security\Permissions\UserPermissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
#[IsGranted(UserPermissions::edit->value, subject: 'user')]
final class AdminUserEditController extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): Response {
        $form = $this->createForm(UserType::class, $user, ['is_creation' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = (string) $form->get('plainPassword')->getData();
            if ('' !== $plainPassword) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }

            $em->flush();

            $this->addFlash('success', 'user.flash.updated');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('@Shared/admin/user/edit.html.twig', [
            'form' => $form,
            'managedUser' => $user,
        ]);
    }
}
