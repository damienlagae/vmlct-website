<?php

declare(strict_types=1);

namespace App\Shared\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Base controller for every admin surface. Centralises the ROLE_ADMIN
 * gate so concrete admin controllers do not have to repeat it.
 *
 * Per-action permissions (e.g. SponsorPermissions::edit) still apply on
 * top of this baseline via #[IsGranted] on the action method.
 */
#[IsGranted('ROLE_ADMIN')]
abstract class AbstractAdminController extends AbstractController
{
}
