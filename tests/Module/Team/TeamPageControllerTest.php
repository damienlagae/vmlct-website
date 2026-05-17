<?php

declare(strict_types=1);

namespace App\Tests\Module\Team;

use App\Module\Team\Entity\RiderCategory;
use App\Module\Team\Entity\StaffRole;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Team\Factory\StaffFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TeamPageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testTeamPageRendersStaffAndRidersGroupedByCategory(): void
    {
        $client = self::createClient();

        StaffFactory::new()->withRole(StaffRole::Coach)->create([
            'firstName' => 'Coach',
            'lastName' => 'Active',
        ]);
        StaffFactory::new()->inactive()->create([
            'firstName' => 'Coach',
            'lastName' => 'Hidden',
        ]);

        RiderFactory::new()->inCategory(RiderCategory::Junioren)->create([
            'firstName' => 'Junior',
            'lastName' => 'Visible',
        ]);
        RiderFactory::new()->inCategory(RiderCategory::Aspiranten)->create([
            'firstName' => 'Aspirant',
            'lastName' => 'Visible',
        ]);
        RiderFactory::new()->inactive()->inCategory(RiderCategory::Junioren)->create([
            'firstName' => 'Hidden',
            'lastName' => 'Rider',
        ]);

        $client->request('GET', '/ploeg');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('h2.section-title', 'Onze ploeg');

        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('Coach Active', $html);
        self::assertStringNotContainsString('Coach Hidden', $html);

        self::assertStringContainsString('Junior Visible', $html);
        self::assertStringContainsString('Aspirant Visible', $html);
        self::assertStringNotContainsString('Hidden Rider', $html);

        // Aspiranten section appears before Junioren (ordered)
        $aspirantenPos = strpos($html, 'Aspiranten');
        $juniorenPos = strpos($html, 'Junioren');
        self::assertNotFalse($aspirantenPos);
        self::assertNotFalse($juniorenPos);
        self::assertLessThan($juniorenPos, $aspirantenPos);
    }

    public function testTeamPageShowsEmptyStatesWhenNoMembers(): void
    {
        $client = self::createClient();
        $client->request('GET', '/ploeg');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Nog geen omkadering', $html);
        self::assertStringContainsString('Nog geen renners', $html);
    }
}
