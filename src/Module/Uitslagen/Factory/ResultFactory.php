<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Factory;

use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Entity\Result;
use App\Module\Uitslagen\Entity\ResultDiscipline;
use App\Module\Uitslagen\Entity\ResultStatus;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Result>
 */
final class ResultFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Result::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'rider' => RiderFactory::new(),
            'raceName' => self::faker()->company(),
            'raceDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', '-1 day')),
            'raceLocation' => self::faker()->city(),
            'discipline' => self::faker()->randomElement(ResultDiscipline::cases()),
            'status' => ResultStatus::Finished,
            'rank' => self::faker()->numberBetween(1, 30),
        ];
    }
}
