<?php

declare(strict_types=1);

namespace App\Shared\Security\Voter;

use App\Shared\Security\PermissionMapProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Role-based voter for domain permissions.
 *
 * Looks up the permission in PermissionMapProvider::$map and grants access
 * if the current user (after role hierarchy resolution) carries any of the
 * allowed roles. Subject-agnostic: works with or without a subject.
 */
final class PermissionVoter extends Voter
{
    public function __construct(
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return PermissionMapProvider::knows($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $reachable = $this->roleHierarchy->getReachableRoleNames($user->getRoles());
        $allowed = PermissionMapProvider::getRoles($attribute);

        foreach ($allowed as $role) {
            if (\in_array($role, $reachable, true)) {
                return true;
            }
        }

        return false;
    }
}
