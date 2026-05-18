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
            'startsAt' => new \DateTimeImmutable('+2 weeks 10:00'),
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Nieuwelingen, RaceCategory::Junioren],
            'description' => 'Klassieker met start en aankomst in Beveren.',
        ]);

        RaceFactory::createOne([
            'name' => 'Memorial Van Moer',
            'startsAt' => new \DateTimeImmutable('+5 weeks 14:00'),
            'location' => 'Lokeren',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Aspiranten, RaceCategory::Nieuwelingen],
            'externalUrl' => 'https://example.com/memorial-van-moer',
        ]);

        RaceFactory::createOne([
            'name' => 'Veldritcross Beveren',
            'startsAt' => new \DateTimeImmutable('-2 weeks 11:00'),
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Cyclocross,
            'categories' => [RaceCategory::Miniemen, RaceCategory::Aspiranten],
            'description' => 'Druk bevochten cross op een technisch parcours.',
        ]);

        RaceFactory::createOne([
            'name' => 'Pistedag Gent',
            'startsAt' => new \DateTimeImmutable('-1 month 19:00'),
            'location' => 'Gent — Eddy Merckxpiste',
            'discipline' => RaceDiscipline::Track,
            'categories' => [RaceCategory::Junioren],
        ]);
    }
}
