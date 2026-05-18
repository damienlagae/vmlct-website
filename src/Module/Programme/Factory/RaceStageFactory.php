<?php

declare(strict_types=1);

namespace App\Module\Programme\Factory;

use App\Module\Programme\Entity\RaceStage;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RaceStage>
 */
final class RaceStageFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return RaceStage::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'race' => RaceFactory::new()->past(),
            'name' => null,
            'stageDate' => null,
            'position' => 1,
        ];
    }
}
