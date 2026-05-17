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

#[Route('/admin/sponsors/new', name: 'admin_sponsor_create', methods: ['GET', 'POST'])]
#[IsGranted(SponsorPermissions::create->value)]
final class AdminSponsorCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $form = $this->createForm(SponsorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sponsor = $form->getData();
            \assert($sponsor instanceof Sponsor);

            $logo = $form->get('logo')->getData();
            if ($logo instanceof UploadedFile) {
                $sponsor->setLogoPath($uploader->upload($logo, 'sponsor'));
            }

            $em->persist($sponsor);
            $em->flush();

            $this->addFlash('success', 'sponsor.flash.created');

            return $this->redirectToRoute('admin_sponsor_index');
        }

        return $this->render('@Sponsor/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
