<?php

declare(strict_types=1);

namespace App\Shared\Security\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/logout', name: 'logout')]
final class LogoutController
{
    public function __invoke(): Response
    {
        throw new \LogicException('Logout is handled by the firewall and must never be reached.');
    }
}
