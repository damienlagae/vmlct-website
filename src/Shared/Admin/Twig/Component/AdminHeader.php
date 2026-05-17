<?php

declare(strict_types=1);

namespace App\Shared\Admin\Twig\Component;

use App\Shared\Security\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Header', template: '@Shared/admin/components/Header.html.twig')]
final class AdminHeader
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function getCurrentUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
