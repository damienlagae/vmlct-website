<?php

declare(strict_types=1);

namespace App\Module\Programme\Story;

use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'programme')]
final class ProgrammeStory extends Story
{
    public function build(): void
    {
        RaceFactory::createOne([
            'name' => 'Heuvelse Pijl',
            'startDate' => new \DateTimeImmutable('+2 weeks'),
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Nieuwelingen, RaceCategory::Junioren],
            'description' => 'Klassieker met start en aankomst in Beveren.',
        ]);

        // Multi-day stage race example
        RaceFactory::createOne([
            'name' => 'Ronde van het Waasland',
            'startDate' => new \DateTimeImmutable('+5 weeks'),
            'endDate' => new \DateTimeImmutable('+5 weeks +2 days'),
            'location' => 'Waasland (3 etappes)',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Aspiranten, RaceCategory::Nieuwelingen],
            'description' => 'Drie-etappekoers door het Waasland.',
        ]);

        RaceFactory::createOne([
            'name' => 'Veldritcross Beveren',
            'startDate' => new \DateTimeImmutable('-2 weeks'),
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Cyclocross,
            'categories' => [RaceCategory::Miniemen, RaceCategory::Aspiranten],
            'description' => 'Druk bevochten cross op een technisch parcours.',
        ]);

        RaceFactory::createOne([
            'name' => 'Pistedag Gent',
            'startDate' => new \DateTimeImmutable('-1 month'),
            'location' => 'Gent — Eddy Merckxpiste',
            'discipline' => RaceDiscipline::Track,
            'categories' => [RaceCategory::Junioren],
        ]);
    }
}
