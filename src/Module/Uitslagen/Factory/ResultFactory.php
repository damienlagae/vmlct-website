<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Factory;

use App\Module\Programme\Factory\RaceStageFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Entity\Result;
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
            'stage' => RaceStageFactory::new(),
            'rank' => self::faker()->numberBetween(1, 30),
        ];
    }
}
