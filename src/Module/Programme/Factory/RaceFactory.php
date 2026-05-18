<?php

declare(strict_types=1);

namespace App\Module\Programme\Factory;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Race>
 */
final class RaceFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Race::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->unique()->company(),
            'startsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-3 months', '+3 months')),
            'location' => self::faker()->city(),
            'discipline' => self::faker()->randomElement(RaceDiscipline::cases()),
            'categories' => [self::faker()->randomElement(RaceCategory::cases())],
        ];
    }

    public function upcoming(): self
    {
        return $this->with([
            'startsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 week', '+3 months')),
        ]);
    }

    public function past(): self
    {
        return $this->with([
            'startsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', '-1 day')),
        ]);
    }
}
