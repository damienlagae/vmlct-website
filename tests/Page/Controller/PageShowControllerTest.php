<?php

declare(strict_types=1);

namespace App\Tests\Page\Controller;

use App\Page\Factory\PageFactory;
use App\Shared\Content\Block\TextBlock;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PageShowControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPublishedPageIsServedThroughTheCatchAllByPath(): void
    {
        $client = self::createClient();
        PageFactory::createOne([
            'title' => 'Over ons',
            'path' => 'over',
            'excerpt' => 'Een korte intro.',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
            'content' => [
                (new TextBlock('<p>Welkom op de pagina.</p>'))->toArray(),
            ],
        ]);

        $client->request('GET', '/over');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Over ons', $html);
        self::assertStringContainsString('Een korte intro', $html);
        self::assertStringContainsString('Welkom op de pagina', $html);
    }

    public function testDraftPageIs404(): void
    {
        $client = self::createClient();
        PageFactory::new()->draft()->create(['path' => 'binnenkort']);

        $client->request('GET', '/binnenkort');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testFuturePublishDateIs404(): void
    {
        $client = self::createClient();
        PageFactory::createOne([
            'path' => 'morgen',
            'publishedAt' => new \DateTimeImmutable('+1 hour'),
        ]);

        $client->request('GET', '/morgen');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testUnknownPathIs404(): void
    {
        $client = self::createClient();

        $client->request('GET', '/this-does-not-exist');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testModuleRoutesAreNotShadowedByTheCatchAll(): void
    {
        $client = self::createClient();
        // create a Page that *would* match a module URL: the module's own
        // route must still win thanks to the catch-all's priority: -100.
        PageFactory::createOne([
            'title' => 'Tries to steal /nieuws',
            'path' => 'nieuws',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
        ]);

        $client->request('GET', '/nieuws');

        // /nieuws is the news index — it must NOT render the Page show.
        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringNotContainsString('Tries to steal', $html);
    }

    public function testNestedPathWithSlashResolves(): void
    {
        $client = self::createClient();
        PageFactory::createOne([
            'title' => 'Wielen',
            'path' => 'uitrusting/wielen',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
        ]);

        $client->request('GET', '/uitrusting/wielen');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Wielen', (string) $client->getResponse()->getContent());
    }
}
