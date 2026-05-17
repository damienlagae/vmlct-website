<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Entity\Staff;
use App\Module\Team\Form\StaffType;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/staff/{id}/edit', name: 'admin_staff_edit', methods: ['GET', 'POST'])]
#[IsGranted(TeamPermissions::edit->value, subject: 'staff')]
final class AdminStaffEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Staff $staff, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'team.flash.staff_updated');

            return $this->redirectToRoute('admin_staff_index');
        }

        return $this->render('@Team/admin/staff/edit.html.twig', [
            'form' => $form,
            'staff' => $staff,
        ]);
    }
}
