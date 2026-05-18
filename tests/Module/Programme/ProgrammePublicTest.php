<?php

declare(strict_types=1);

namespace App\Tests\Module\Programme;

use App\Module\Programme\Entity\RaceCategory;
use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProgrammePublicTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexRendersUpcomingAndPastRacesInSeparateBuckets(): void
    {
        $client = self::createClient();

        RaceFactory::new()->upcoming()->create([
            'name' => 'Heuvelse Pijl',
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Road,
            'categories' => [RaceCategory::Junioren],
        ]);
        RaceFactory::new()->past()->create([
            'name' => 'Veldritcross Beveren',
            'location' => 'Beveren',
            'discipline' => RaceDiscipline::Cyclocross,
        ]);

        $client->request('GET', '/programma');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Heuvelse Pijl', $html);
        self::assertStringContainsString('Veldritcross Beveren', $html);
        self::assertStringContainsString('Beveren', $html);
        self::assertStringContainsString('Junioren', $html);
    }

    public function testMultiStageRaceRendersDateRange(): void
    {
        $client = self::createClient();

        RaceFactory::createOne([
            'name' => 'Driedaagse',
            'startDate' => new \DateTimeImmutable('+1 week'),
            'endDate' => new \DateTimeImmutable('+1 week +2 days'),
            'location' => 'Waasland',
            'discipline' => RaceDiscipline::Road,
        ]);

        $client->request('GET', '/programma');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('race-card__until', $html);
    }

    public function testIndexShowsEmptyStateWhenNoRaces(): void
    {
        $client = self::createClient();
        $client->request('GET', '/programma');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Nog geen koersen', $html);
    }
}
