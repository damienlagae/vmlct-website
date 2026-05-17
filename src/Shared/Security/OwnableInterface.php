<?php

declare(strict_types=1);

namespace App\Shared\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Implemented by entities that have a single "owner" user.
 *
 * The OwnerVoter consults this contract when an ownable permission is
 * voted on with the entity as subject — granting access if the current
 * user is the owner.
 */
interface OwnableInterface
{
    public function getOwner(): ?UserInterface;
}
