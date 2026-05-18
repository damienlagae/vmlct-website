<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Story;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Entity\RaceStage;
use App\Module\Programme\Factory\RaceFactory;
use App\Module\Programme\Factory\RaceStageFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Factory\ResultFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'uitslagen')]
final class UitslagenStory extends Story
{
    public function build(): void
    {
        $riders = RiderFactory::createMany(6);

        // Single-day regional race — 1 implicit stage with no name/date.
        $regional = RaceFactory::createOne([
            'name' => 'Regiokoers Kruibeke',
            'startDate' => new \DateTimeImmutable('-3 weeks'),
            'location' => 'Kruibeke',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Nieuwelingen],
        ]);
        \assert($regional instanceof Race);
        $stage1 = RaceStageFactory::createOne(['race' => $regional, 'position' => 1]);
        \assert($stage1 instanceof RaceStage);
        foreach ([1, 2, 3, 4] as $i => $rank) {
            ResultFactory::createOne([
                'rider' => $riders[$i],
                'stage' => $stage1,
                'rank' => $rank,
            ]);
        }

        // Multi-stage race with two stages, two riders each.
        $stageRace = RaceFactory::createOne([
            'name' => 'Tweedaagse van Beveren',
            'startDate' => new \DateTimeImmutable('-1 month'),
            'endDate' => new \DateTimeImmutable('-1 month +1 day'),
            'location' => 'Beveren (etappes)',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Junioren],
        ]);
        \assert($stageRace instanceof Race);
        $st1 = RaceStageFactory::createOne([
            'race' => $stageRace,
            'name' => 'Etappe 1 — Beveren → Sint-Niklaas',
            'stageDate' => new \DateTimeImmutable('-1 month'),
            'position' => 1,
        ]);
        $st2 = RaceStageFactory::createOne([
            'race' => $stageRace,
            'name' => 'Etappe 2 — Sint-Niklaas → Beveren',
            'stageDate' => new \DateTimeImmutable('-1 month +1 day'),
            'position' => 2,
        ]);
        \assert($st1 instanceof RaceStage);
        \assert($st2 instanceof RaceStage);
        ResultFactory::createOne(['rider' => $riders[4], 'stage' => $st1, 'rank' => 2]);
        ResultFactory::createOne(['rider' => $riders[5], 'stage' => $st1, 'rank' => 5]);
        ResultFactory::createOne(['rider' => $riders[4], 'stage' => $st2, 'rank' => 1]);
        ResultFactory::createOne(['rider' => $riders[5], 'stage' => $st2, 'rank' => 8]);
    }
}
