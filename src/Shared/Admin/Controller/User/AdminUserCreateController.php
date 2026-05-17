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

#[Route('/admin/users/new', name: 'admin_user_create', methods: ['GET', 'POST'])]
#[IsGranted(UserPermissions::create->value)]
final class AdminUserCreateController extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): Response {
        $form = $this->createForm(UserType::class, null, ['is_creation' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            \assert($user instanceof User);

            $plainPassword = (string) $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'user.flash.created');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('@Shared/admin/user/create.html.twig', [
            'form' => $form,
        ]);
    }
}
