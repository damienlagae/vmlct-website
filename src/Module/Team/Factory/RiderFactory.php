<?php

declare(strict_types=1);

namespace App\Module\Team\Factory;

use App\Module\Team\Entity\Rider;
use App\Module\Team\Entity\RiderCategory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Rider>
 */
final class RiderFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Rider::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        $category = self::faker()->randomElement(RiderCategory::cases());
        \assert($category instanceof RiderCategory);

        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'dateOfBirth' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-18 years', '-8 years')),
            'category' => $category,
            'photoUrl' => null,
            'active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->with(['active' => false]);
    }

    public function inCategory(RiderCategory $category): self
    {
        return $this->with(['category' => $category]);
    }
}
