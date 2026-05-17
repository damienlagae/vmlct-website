<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Entity\Staff;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Media\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/staff/{id}/delete', name: 'admin_staff_delete', methods: ['POST'])]
#[IsGranted(TeamPermissions::delete->value, subject: 'staff')]
final class AdminStaffDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Staff $staff, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-staff-'.$staff->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $photoPath = $staff->getPhotoPath();

        $em->remove($staff);
        $em->flush();

        $uploader->delete($photoPath);

        $this->addFlash('success', 'team.flash.staff_deleted');

        return $this->redirectToRoute('admin_staff_index');
    }
}
