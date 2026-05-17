<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Controller\Admin;

use App\Module\Sponsor\Entity\Sponsor;
use App\Module\Sponsor\Form\SponsorType;
use App\Module\Sponsor\Security\SponsorPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Media\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sponsors/{id}/edit', name: 'admin_sponsor_edit', methods: ['GET', 'POST'])]
#[IsGranted(SponsorPermissions::edit->value, subject: 'sponsor')]
final class AdminSponsorEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Sponsor $sponsor, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();
            if ($logo instanceof UploadedFile) {
                $uploader->delete($sponsor->getLogoPath());
                $sponsor->setLogoPath($uploader->upload($logo, 'sponsor'));
            }

            $em->flush();

            $this->addFlash('success', 'sponsor.flash.updated');

            return $this->redirectToRoute('admin_sponsor_index');
        }

        return $this->render('@Sponsor/admin/edit.html.twig', [
            'form' => $form,
            'sponsor' => $sponsor,
        ]);
    }
}
