<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Entity\Rider;
use App\Module\Team\Form\RiderType;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use App\Shared\Media\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/renners/{id}/edit', name: 'admin_rider_edit', methods: ['GET', 'POST'])]
#[IsGranted(TeamPermissions::edit->value, subject: 'rider')]
final class AdminRiderEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Rider $rider, EntityManagerInterface $em, Uploader $uploader): Response
    {
        $form = $this->createForm(RiderType::class, $rider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo instanceof UploadedFile) {
                $uploader->delete($rider->getPhotoPath());
                $rider->setPhotoPath($uploader->upload($photo, 'rider'));
            }

            $em->flush();

            $this->addFlash('success', 'team.flash.rider_updated');

            return $this->redirectToRoute('admin_rider_index');
        }

        return $this->render('@Team/admin/rider/edit.html.twig', [
            'form' => $form,
            'rider' => $rider,
        ]);
    }
}
