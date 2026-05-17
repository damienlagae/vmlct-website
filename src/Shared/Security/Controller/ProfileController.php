<?php

declare(strict_types=1);

namespace App\Shared\Security\Controller;

use App\Shared\Security\Entity\User;
use App\Shared\Security\PermissionRegistry;
use App\Shared\Security\Permissions\PermissionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profiel', name: 'profile')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ProfileController extends AbstractController
{
    public function __invoke(
        PermissionRegistry $registry,
        Security $security,
    ): Response {
        $user = $this->getUser();
        \assert($user instanceof User);

        $grantedByDomain = [];
        foreach ($registry->all() as $case) {
            if (!$security->isGranted($case->value)) {
                continue;
            }
            $domain = self::domainFor($case);
            $grantedByDomain[$domain][] = $case;
        }
        ksort($grantedByDomain);

        return $this->render('@Shared/security/profile.html.twig', [
            'profileUser' => $user,
            'grantedByDomain' => $grantedByDomain,
        ]);
    }

    private static function domainFor(PermissionInterface $case): string
    {
        \assert(\is_string($case->value));
        $parts = explode('_', $case->value, 2);

        return ucfirst(strtolower($parts[0]));
    }
}
