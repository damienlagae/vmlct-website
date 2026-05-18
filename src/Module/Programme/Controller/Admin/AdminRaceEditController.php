<?php

declare(strict_types=1);

namespace App\Module\Programme\Controller\Admin;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Form\RaceType;
use App\Module\Programme\Security\ProgrammePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/programme/{id}/edit', name: 'admin_race_edit', methods: ['GET', 'POST'])]
#[IsGranted(ProgrammePermissions::edit->value, subject: 'race')]
final class AdminRaceEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Race $race, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'programme.flash.updated');

            return $this->redirectToRoute('admin_race_index');
        }

        return $this->render('@Programme/admin/edit.html.twig', [
            'form' => $form,
            'race' => $race,
        ]);
    }
}
