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

#[Route('/admin/uitslagen/new', name: 'admin_result_create', methods: ['GET', 'POST'])]
#[IsGranted(UitslagenPermissions::create->value)]
final class AdminResultCreateController extends AbstractAdminController
{
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ResultType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $form->getData();
            \assert($result instanceof Result);

            $em->persist($result);
            $em->flush();

            $this->addFlash('success', 'uitslagen.flash.created');

            return $this->redirectToRoute('admin_result_index');
        }

        return $this->render('@Uitslagen/admin/create.html.twig', [
            'form' => $form,
        ]);
    }
}
