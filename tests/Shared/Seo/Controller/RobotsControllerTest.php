<?php

declare(strict_types=1);

namespace App\Tests\Shared\Seo\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RobotsControllerTest extends WebTestCase
{
    public function testRobotsTxtAdvertisesSitemap(): void
    {
        $client = self::createClient();
        $client->request('GET', '/robots.txt');

        self::assertResponseIsSuccessful();
        self::assertStringStartsWith('text/plain', (string) $client->getResponse()->headers->get('Content-Type'));

        $body = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('User-agent: *', $body);
        self::assertStringContainsString('Disallow: /admin', $body);
        self::assertStringContainsString('Sitemap:', $body);
        self::assertStringContainsString('/sitemap.xml', $body);
    }
}
