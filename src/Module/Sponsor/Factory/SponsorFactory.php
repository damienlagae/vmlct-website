<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Factory;

use App\Module\Sponsor\Entity\Sponsor;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Sponsor>
 */
final class SponsorFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Sponsor::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->company(),
            'websiteUrl' => self::faker()->url(),
            'logoPath' => null,
            'displayOrder' => self::faker()->numberBetween(0, 100),
            'active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->with(['active' => false]);
    }

    public function withOrder(int $order): self
    {
        return $this->with(['displayOrder' => $order]);
    }
}
