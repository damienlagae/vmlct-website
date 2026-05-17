<?php

declare(strict_types=1);

namespace App\Shared\Security\Factory;

use App\Shared\Security\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->safeEmail(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'roles' => [],
            'active' => true,
        ];
    }

    public function withPlainPassword(string $password): self
    {
        return $this->afterInstantiate(function (User $user) use ($password): void {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        });
    }

    public function admin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    public function superAdmin(): self
    {
        return $this->with(['roles' => ['ROLE_SUPER_ADMIN']]);
    }

    public function inactive(): self
    {
        return $this->with(['active' => false]);
    }
}
