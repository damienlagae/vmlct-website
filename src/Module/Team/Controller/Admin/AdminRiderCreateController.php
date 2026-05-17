<?php

declare(strict_types=1);

namespace App\Module\Team\Controller\Admin;

use App\Module\Team\Entity\Rider;
use App\Module\Team\Form\RiderType;
use App\Module\Team\Security\TeamPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/team/renners/new', name: 'admin_rider_create', methods: ['GET', 'POST'])]
#[IsGranted(TeamPermissions::create->value)]
final class AdminRiderCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RiderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rider = $form->getData();
            \assert($rider instanceof Rider);

            $em->persist($rider);
            $em->flush();

            $this->addFlash('success', 'team.flash.rider_created');

            return $this->redirectToRoute('admin_rider_index');
        }

        return $this->render('@Team/admin/rider/create.html.twig', [
            'form' => $form,
        ]);
    }
}
