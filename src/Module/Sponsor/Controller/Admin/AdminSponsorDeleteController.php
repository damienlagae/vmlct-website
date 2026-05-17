<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Controller\Admin;

use App\Module\Sponsor\Entity\Sponsor;
use App\Module\Sponsor\Security\SponsorPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Media\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sponsors/{id}/delete', name: 'admin_sponsor_delete', methods: ['POST'])]
#[IsGranted(SponsorPermissions::delete->value, subject: 'sponsor')]
final class AdminSponsorDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Sponsor $sponsor, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-sponsor-'.$sponsor->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $logoPath = $sponsor->getLogoPath();

        $em->remove($sponsor);
        $em->flush();

        $uploader->delete($logoPath);

        $this->addFlash('success', 'sponsor.flash.deleted');

        return $this->redirectToRoute('admin_sponsor_index');
    }
}
