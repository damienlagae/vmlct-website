<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Controller\Admin;

use App\Module\Uitslagen\Entity\Result;
use App\Module\Uitslagen\Security\UitslagenPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/uitslagen/{id}/delete', name: 'admin_result_delete', methods: ['POST'])]
#[IsGranted(UitslagenPermissions::delete->value, subject: 'result')]
final class AdminResultDeleteController extends AbstractAdminController
{
    public function __invoke(Request $request, Result $result, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if (!$this->isCsrfTokenValid('delete-result-'.$result->getId(), $token)) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($result);
        $em->flush();

        $this->addFlash('success', 'uitslagen.flash.deleted');

        return $this->redirectToRoute('admin_result_index');
    }
}
