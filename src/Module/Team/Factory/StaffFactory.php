<?php

declare(strict_types=1);

namespace App\Module\Team\Factory;

use App\Module\Team\Entity\Staff;
use App\Module\Team\Entity\StaffRole;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Staff>
 */
final class StaffFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Staff::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        $role = self::faker()->randomElement(StaffRole::cases());
        \assert($role instanceof StaffRole);

        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'role' => $role,
            'photoUrl' => null,
            'active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->with(['active' => false]);
    }

    public function withRole(StaffRole $role): self
    {
        return $this->with(['role' => $role]);
    }
}
