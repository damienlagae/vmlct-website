<?php

declare(strict_types=1);

namespace App\Module\Programme\Controller\Admin;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Security\ProgrammePermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/programme/{id}/delete', name: 'admin_race_delete', methods: ['POST'])]
#[IsGranted(ProgrammePermissions::delete->value, subject: 'race')]
final class AdminRaceDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Race $race, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-race-'.$race->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($race);
        $em->flush();

        $this->addFlash('success', 'programme.flash.deleted');

        return $this->redirectToRoute('admin_race_index');
    }
}
