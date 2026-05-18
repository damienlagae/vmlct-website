<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Story;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Repository\RaceRepository;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Entity\ResultDiscipline;
use App\Module\Uitslagen\Entity\ResultStatus;
use App\Module\Uitslagen\Factory\ResultFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'uitslagen')]
final class UitslagenStory extends Story
{
    public function __construct(private readonly RaceRepository $raceRepository)
    {
    }

    public function build(): void
    {
        // Stable rider pool for the fixture.
        $riders = RiderFactory::createMany(6);

        // Standalone race (regional, no Race row) — full top-3 + DNF.
        ResultFactory::createOne([
            'rider' => $riders[0],
            'raceName' => 'Regiokoers Kruibeke',
            'raceLocation' => 'Kruibeke',
            'raceDate' => new \DateTimeImmutable('-3 weeks'),
            'discipline' => ResultDiscipline::Road,
            'status' => ResultStatus::Finished,
            'rank' => 1,
        ]);
        ResultFactory::createOne([
            'rider' => $riders[1],
            'raceName' => 'Regiokoers Kruibeke',
            'raceLocation' => 'Kruibeke',
            'raceDate' => new \DateTimeImmutable('-3 weeks'),
            'discipline' => ResultDiscipline::Road,
            'status' => ResultStatus::Finished,
            'rank' => 2,
        ]);
        ResultFactory::createOne([
            'rider' => $riders[2],
            'raceName' => 'Regiokoers Kruibeke',
            'raceLocation' => 'Kruibeke',
            'raceDate' => new \DateTimeImmutable('-3 weeks'),
            'discipline' => ResultDiscipline::Road,
            'status' => ResultStatus::Finished,
            'rank' => 3,
        ]);
        ResultFactory::createOne([
            'rider' => $riders[3],
            'raceName' => 'Regiokoers Kruibeke',
            'raceLocation' => 'Kruibeke',
            'raceDate' => new \DateTimeImmutable('-3 weeks'),
            'discipline' => ResultDiscipline::Road,
            'status' => ResultStatus::Dnf,
            'rank' => null,
        ]);

        // Linked to a Programme Race if any past race exists.
        $past = $this->raceRepository->findPast(1);
        if ($past !== [] && $past[0] instanceof Race) {
            $race = $past[0];
            ResultFactory::createOne([
                'rider' => $riders[4],
                'race' => $race,
                'raceDate' => \DateTimeImmutable::createFromInterface($race->getStartsAt()),
                'status' => ResultStatus::Finished,
                'rank' => 5,
            ]);
            ResultFactory::createOne([
                'rider' => $riders[5],
                'race' => $race,
                'raceDate' => \DateTimeImmutable::createFromInterface($race->getStartsAt()),
                'status' => ResultStatus::Dns,
                'rank' => null,
            ]);
        }
    }
}
