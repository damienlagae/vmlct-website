<?php

declare(strict_types=1);

namespace App\Shared\Security\Console;

use App\Shared\Security\Entity\User;
use App\Shared\Security\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user account.',
)]
final class CreateUserCommand
{
    private const ROLES = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $users,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Email address (used as the login identifier)')]
        string $email,
        #[Argument(description: 'Plain password. If omitted, you will be prompted (hidden input).')]
        ?string $password = null,
        #[Option(description: 'Role to assign', suggestedValues: self::ROLES)]
        string $role = 'ROLE_USER',
        #[Option(description: 'First name')]
        string $firstName = 'New',
        #[Option(description: 'Last name')]
        string $lastName = 'User',
        #[Option(description: 'Create the user as inactive')]
        bool $inactive = false,
    ): int {
        if (!\in_array($role, self::ROLES, true)) {
            $io->error(sprintf('Unknown role "%s". Allowed: %s.', $role, implode(', ', self::ROLES)));

            return Command::INVALID;
        }

        if (null !== $this->users->findOneByEmail($email)) {
            $io->error(sprintf('A user with email "%s" already exists.', $email));

            return Command::FAILURE;
        }

        $password ??= $io->askHidden('Password (hidden)');
        if (null === $password || '' === $password) {
            $io->error('Password cannot be empty.');

            return Command::INVALID;
        }

        $user = new User($email, $firstName, $lastName);
        $user->setRoles([$role]);
        $user->setActive(!$inactive);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('User "%s" created with role %s.', $email, $role));

        return Command::SUCCESS;
    }
}
