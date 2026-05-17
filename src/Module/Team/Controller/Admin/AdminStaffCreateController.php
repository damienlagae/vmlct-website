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

#[Route('/admin/team/staff/new', name: 'admin_staff_create', methods: ['GET', 'POST'])]
#[IsGranted(TeamPermissions::create->value)]
final class AdminStaffCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StaffType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $staff = $form->getData();
            \assert($staff instanceof Staff);

            $em->persist($staff);
            $em->flush();

            $this->addFlash('success', 'team.flash.staff_created');

            return $this->redirectToRoute('admin_staff_index');
        }

        return $this->render('@Team/admin/staff/create.html.twig', [
            'form' => $form,
        ]);
    }
}
