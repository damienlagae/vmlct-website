<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Controller\Admin;

use App\Module\Uitslagen\Entity\Result;
use App\Module\Uitslagen\Form\ResultType;
use App\Module\Uitslagen\Security\UitslagenPermissions;
use App\Shared\Admin\Controller\AbstractAdminController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/uitslagen/{id}/edit', name: 'admin_result_edit', methods: ['GET', 'POST'])]
#[IsGranted(UitslagenPermissions::edit->value, subject: 'result')]
final class AdminResultEditController extends AbstractAdminController
{
    public function __invoke(Request $request, Result $result, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ResultType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'uitslagen.flash.updated');

            return $this->redirectToRoute('admin_result_index');
        }

        return $this->render('@Uitslagen/admin/edit.html.twig', [
            'form' => $form,
            'result' => $result,
        ]);
    }
}
