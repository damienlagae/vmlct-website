<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Story;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
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

        // Single-day regional race + a tight top-3.
        $regional = RaceFactory::createOne([
            'name' => 'Regiokoers Kruibeke',
            'startDate' => new \DateTimeImmutable('-3 weeks'),
            'location' => 'Kruibeke',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Nieuwelingen],
        ]);
        $finishOrder = [$riders[0], $riders[1], $riders[2], $riders[3]];
        foreach ($finishOrder as $i => $rider) {
            ResultFactory::createOne([
                'rider' => $rider,
                'race' => $regional,
                'rank' => $i + 1,
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
        ResultFactory::createOne(['rider' => $riders[4], 'race' => $stageRace, 'stageNumber' => 1, 'rank' => 2]);
        ResultFactory::createOne(['rider' => $riders[5], 'race' => $stageRace, 'stageNumber' => 1, 'rank' => 5]);
        ResultFactory::createOne(['rider' => $riders[4], 'race' => $stageRace, 'stageNumber' => 2, 'rank' => 1]);
        ResultFactory::createOne(['rider' => $riders[5], 'race' => $stageRace, 'stageNumber' => 2, 'rank' => 8]);
    }
}
