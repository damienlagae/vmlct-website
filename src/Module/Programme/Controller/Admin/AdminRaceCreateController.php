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

#[Route('/admin/programme/new', name: 'admin_race_create', methods: ['GET', 'POST'])]
#[IsGranted(ProgrammePermissions::create->value)]
final class AdminRaceCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RaceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $race = $form->getData();
            \assert($race instanceof Race);

            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'programme.flash.created');

            return $this->redirectToRoute('admin_race_index');
        }

        return $this->render('@Programme/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
