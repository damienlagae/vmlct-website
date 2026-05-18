<?php

declare(strict_types=1);

namespace App\Tests\Module\Uitslagen;

use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use App\Module\Programme\Factory\RaceStageFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Factory\ResultFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UitslagenPublicTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexShowsRecentResultsGroupedByStage(): void
    {
        $client = self::createClient();

        $race = RaceFactory::createOne([
            'name' => 'Regiokoers Kruibeke',
            'startDate' => new \DateTimeImmutable('-1 week'),
            'location' => 'Kruibeke',
            'discipline' => RaceDiscipline::Road,
        ]);
        $stage = RaceStageFactory::createOne(['race' => $race, 'position' => 1]);
        $rider = RiderFactory::createOne(['firstName' => 'Lars', 'lastName' => 'Test']);
        ResultFactory::createOne(['rider' => $rider, 'stage' => $stage, 'rank' => 3]);

        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Regiokoers Kruibeke', $html);
        self::assertStringContainsString('Lars Test', $html);
        self::assertStringContainsString('Kruibeke', $html);
        self::assertStringContainsString('uitslagen-podium__slot--rank3', $html);
    }

    public function testMultiStageRaceRendersOneGroupPerStage(): void
    {
        $client = self::createClient();

        $race = RaceFactory::createOne([
            'name' => 'Tweedaagse',
            'startDate' => new \DateTimeImmutable('-1 week'),
            'endDate' => new \DateTimeImmutable('-1 week +1 day'),
            'discipline' => RaceDiscipline::Road,
        ]);
        $stage1 = RaceStageFactory::createOne(['race' => $race, 'name' => 'Prologue', 'position' => 1]);
        $stage2 = RaceStageFactory::createOne(['race' => $race, 'name' => 'Etappe 1', 'position' => 2]);
        $rider = RiderFactory::createOne();
        ResultFactory::createOne(['rider' => $rider, 'stage' => $stage1, 'rank' => 1]);
        ResultFactory::createOne(['rider' => $rider, 'stage' => $stage2, 'rank' => 2]);

        $client->request('GET', '/uitslagen');
        $html = (string) $client->getResponse()->getContent();

        self::assertSame(2, substr_count($html, 'uitslagen-group__title'));
        self::assertStringContainsString('Prologue', $html);
        self::assertStringContainsString('Etappe 1', $html);
    }

    public function testEmptyStateRendered(): void
    {
        $client = self::createClient();
        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Nog geen uitslagen', (string) $client->getResponse()->getContent());
    }
}
