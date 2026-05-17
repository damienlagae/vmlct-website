<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security\Voter;

use App\Shared\Security\OwnableInterface;
use App\Shared\Security\PermissionRegistry;
use App\Shared\Security\Permissions\PermissionInterface;
use App\Shared\Security\Voter\OwnerVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

enum FakeOwnerPermissions: string implements PermissionInterface
{
    case viewOwn = 'FAKE_VIEW_OWN';
    case viewAny = 'FAKE_VIEW_ANY';

    public function isOwnable(): bool
    {
        return match ($this) {
            self::viewOwn => true,
            self::viewAny => false,
        };
    }
}

final class OwnerVoterTest extends TestCase
{
    public function testGrantsAccessWhenUserIsTheOwner(): void
    {
        $owner = $this->createUser('alice@example.com');
        $subject = $this->createOwnable($owner);
        $voter = new OwnerVoter(new PermissionRegistry([FakeOwnerPermissions::class]));

        $token = $this->tokenFor($owner);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($token, $subject, [FakeOwnerPermissions::viewOwn->value]),
        );
    }

    public function testDeniesWhenUserIsNotTheOwner(): void
    {
        $owner = $this->createUser('alice@example.com');
        $other = $this->createUser('bob@example.com');
        $subject = $this->createOwnable($owner);
        $voter = new OwnerVoter(new PermissionRegistry([FakeOwnerPermissions::class]));

        $token = $this->tokenFor($other);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, $subject, [FakeOwnerPermissions::viewOwn->value]),
        );
    }

    public function testAbstainsWhenPermissionIsNotOwnable(): void
    {
        $owner = $this->createUser('alice@example.com');
        $subject = $this->createOwnable($owner);
        $voter = new OwnerVoter(new PermissionRegistry([FakeOwnerPermissions::class]));

        $token = $this->tokenFor($owner);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($token, $subject, [FakeOwnerPermissions::viewAny->value]),
        );
    }

    public function testAbstainsWhenSubjectIsNotOwnable(): void
    {
        $voter = new OwnerVoter(new PermissionRegistry([FakeOwnerPermissions::class]));
        $token = $this->tokenFor($this->createUser('alice@example.com'));

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($token, new \stdClass(), [FakeOwnerPermissions::viewOwn->value]),
        );
    }

    private function createUser(string $email): UserInterface
    {
        $user = $this->createStub(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn($email);
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        return $user;
    }

    private function createOwnable(UserInterface $owner): OwnableInterface
    {
        return new class ($owner) implements OwnableInterface {
            public function __construct(private readonly UserInterface $owner)
            {
            }

            public function getOwner(): ?UserInterface
            {
                return $this->owner;
            }
        };
    }

    private function tokenFor(UserInterface $user): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
