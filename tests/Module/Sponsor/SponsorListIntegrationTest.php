<?php

declare(strict_types=1);

namespace App\Tests\Module\Sponsor;

use App\Module\Sponsor\Factory\SponsorFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class SponsorListIntegrationTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testActiveSponsorsAreRenderedOnHomeInDisplayOrder(): void
    {
        $client = self::createClient();

        SponsorFactory::createOne(['name' => 'Zeta Visible', 'displayOrder' => 30, 'active' => true]);
        SponsorFactory::createOne(['name' => 'Alpha Visible', 'displayOrder' => 10, 'active' => true]);
        SponsorFactory::createOne(['name' => 'Beta Visible', 'displayOrder' => 20, 'active' => true]);
        SponsorFactory::createOne(['name' => 'Hidden Sponsor', 'displayOrder' => 0, 'active' => false]);

        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('Alpha Visible', $html);
        self::assertStringContainsString('Beta Visible', $html);
        self::assertStringContainsString('Zeta Visible', $html);
        self::assertStringNotContainsString('Hidden Sponsor', $html);

        $alpha = strpos($html, 'Alpha Visible');
        $beta = strpos($html, 'Beta Visible');
        $zeta = strpos($html, 'Zeta Visible');

        self::assertNotFalse($alpha);
        self::assertNotFalse($beta);
        self::assertNotFalse($zeta);
        self::assertLessThan($beta, $alpha, 'Alpha (order 10) should appear before Beta (order 20)');
        self::assertLessThan($zeta, $beta, 'Beta (order 20) should appear before Zeta (order 30)');
    }

    public function testEmptyStateRenderedWhenNoActiveSponsors(): void
    {
        $client = self::createClient();

        SponsorFactory::createOne(['name' => 'Only Inactive', 'active' => false]);

        $client->request('GET', '/');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertStringNotContainsString('Only Inactive', (string) $client->getResponse()->getContent());
        self::assertSelectorTextContains('#sponsors em', 'Nog geen sponsors');
    }
}
