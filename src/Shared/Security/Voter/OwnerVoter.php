<?php

declare(strict_types=1);

namespace App\Shared\Security\Voter;

use App\Shared\Security\OwnableInterface;
use App\Shared\Security\PermissionRegistry;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Subject-based voter granting access when the current user owns the
 * subject and the permission case declares itself as ownable.
 *
 * Works in tandem with PermissionVoter via the affirmative strategy:
 * either voter granting is enough.
 */
final class OwnerVoter extends Voter
{
    public function __construct(
        private readonly PermissionRegistry $registry,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof OwnableInterface) {
            return false;
        }

        $permission = $this->registry->find($attribute);

        return null !== $permission && $permission->isOwnable();
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        \assert($subject instanceof OwnableInterface);

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        $owner = $subject->getOwner();
        if (null === $owner) {
            return false;
        }

        return $owner->getUserIdentifier() === $user->getUserIdentifier();
    }
}
