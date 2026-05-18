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
            'startDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-3 months', '+3 months'))->setTime(0, 0),
            'location' => self::faker()->city(),
            'discipline' => RaceDiscipline::Road,
            'categories' => [self::faker()->randomElement(RaceCategory::cases())],
        ];
    }

    public function upcoming(): self
    {
        return $this->with([
            'startDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 week', '+3 months'))->setTime(0, 0),
        ]);
    }

    public function past(): self
    {
        return $this->with([
            'startDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', '-1 day'))->setTime(0, 0),
        ]);
    }
}
