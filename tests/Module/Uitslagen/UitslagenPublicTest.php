<?php

declare(strict_types=1);

namespace App\Tests\Module\Uitslagen;

use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Entity\ResultDiscipline;
use App\Module\Uitslagen\Entity\ResultStatus;
use App\Module\Uitslagen\Factory\ResultFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UitslagenPublicTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexShowsRecentResults(): void
    {
        $client = self::createClient();

        $rider = RiderFactory::createOne(['firstName' => 'Lars', 'lastName' => 'Test']);
        ResultFactory::createOne([
            'rider' => $rider,
            'raceName' => 'Regiokoers Kruibeke',
            'raceLocation' => 'Kruibeke',
            'raceDate' => new \DateTimeImmutable('-1 week'),
            'discipline' => ResultDiscipline::Road,
            'status' => ResultStatus::Finished,
            'rank' => 3,
        ]);

        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Regiokoers Kruibeke', $html);
        self::assertStringContainsString('Lars Test', $html);
        self::assertStringContainsString('Kruibeke', $html);
        // Rank 3 surfaces as the bronze podium slot.
        self::assertStringContainsString('uitslagen-podium__slot--rank3', $html);
    }

    public function testEffectiveRaceFieldsFollowLinkedRace(): void
    {
        $client = self::createClient();

        $race = RaceFactory::createOne([
            'name' => 'Memorial Van Moer',
            'location' => 'Lokeren',
            'startsAt' => new \DateTimeImmutable('-2 weeks'),
            'discipline' => RaceDiscipline::Road,
        ]);

        ResultFactory::createOne([
            'race' => $race,
            // Local fallback values should be ignored because race is set.
            'raceName' => 'WRONG (should be ignored)',
            'raceLocation' => 'WRONG-LOC',
            'rank' => 1,
        ]);

        $client->request('GET', '/uitslagen');
        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('Memorial Van Moer', $html);
        self::assertStringContainsString('Lokeren', $html);
        self::assertStringNotContainsString('WRONG (should be ignored)', $html);
    }

    public function testEmptyStateRendered(): void
    {
        $client = self::createClient();
        $client->request('GET', '/uitslagen');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Nog geen uitslagen', (string) $client->getResponse()->getContent());
    }
}
