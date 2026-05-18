<?php

declare(strict_types=1);

namespace App\Tests\Module\Uitslagen;

use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Factory\ResultFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UitslagenPublicTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexShowsRecentResultsGroupedByRace(): void
    {
        $client = self::createClient();

        $race = RaceFactory::createOne([
            'name' => 'Regiokoers Kruibeke',
            'startDate' => new \DateTimeImmutable('-1 week'),
            'location' => 'Kruibeke',
            'discipline' => RaceDiscipline::Road,
        ]);
        $rider = RiderFactory::createOne(['firstName' => 'Lars', 'lastName' => 'Test']);
        ResultFactory::createOne(['rider' => $rider, 'race' => $race, 'rank' => 3]);

        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Regiokoers Kruibeke', $html);
        self::assertStringContainsString('Lars Test', $html);
        self::assertStringContainsString('Kruibeke', $html);
        // Rank 3 = bronze podium slot
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
        $rider = RiderFactory::createOne();
        ResultFactory::createOne(['rider' => $rider, 'race' => $race, 'stageNumber' => 1, 'rank' => 1]);
        ResultFactory::createOne(['rider' => $rider, 'race' => $race, 'stageNumber' => 2, 'rank' => 2]);

        $client->request('GET', '/uitslagen');
        $html = (string) $client->getResponse()->getContent();

        // Two groups → two `uitslagen-group__title` headers + stage suffixes.
        self::assertSame(2, substr_count($html, 'uitslagen-group__title'));
        self::assertStringContainsString('Etappe 1', $html);
        self::assertStringContainsString('Etappe 2', $html);
    }

    public function testEmptyStateRendered(): void
    {
        $client = self::createClient();
        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Nog geen uitslagen', (string) $client->getResponse()->getContent());
    }
}
