<?php

declare(strict_types=1);

namespace App\Tests\Module\Menu;

use App\Module\Menu\Entity\MenuTargetType;
use App\Module\Menu\Factory\MenuItemFactory;
use App\Page\Factory\PageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class MenuRenderingTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testRoutesItemRendersOnFrontHeader(): void
    {
        $client = self::createClient();

        MenuItemFactory::createOne([
            'label' => 'Mijn nieuws',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'news_index',
            'position' => 0,
        ]);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Mijn nieuws', $html);
        self::assertStringContainsString('href="/nieuws"', $html);
    }

    public function testPageItemFollowsThePagePath(): void
    {
        $client = self::createClient();

        $page = PageFactory::createOne([
            'title' => 'Over',
            'path' => 'over-ons-2026',
            'publishedAt' => new \DateTimeImmutable('-1 hour'),
        ]);
        MenuItemFactory::createOne([
            'label' => 'Over',
            'targetType' => MenuTargetType::Page,
            'page' => $page,
            'position' => 0,
        ]);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('href="/over-ons-2026"', $html);
    }

    public function testInactiveItemsAreHidden(): void
    {
        $client = self::createClient();

        MenuItemFactory::createOne([
            'label' => 'Visible',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 0,
            'active' => true,
        ]);
        MenuItemFactory::createOne([
            'label' => 'Hidden',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 1,
            'active' => false,
        ]);

        $client->request('GET', '/');
        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('Visible', $html);
        self::assertStringNotContainsString('>Hidden<', $html);
    }

    public function testDanglingPageReferenceDoesNotCrashTheNavbar(): void
    {
        $client = self::createClient();

        // Page intentionally not created → menu item points to nothing.
        MenuItemFactory::createOne([
            'label' => 'Broken',
            'targetType' => MenuTargetType::Page,
            'page' => null,
            'position' => 0,
        ]);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Broken', $html);
        // Dangling pages render as href="#" — not a 500.
        self::assertStringContainsString('href="#"', $html);
    }

    public function testChildrenAppearAsDropdown(): void
    {
        $client = self::createClient();

        $parent = MenuItemFactory::createOne([
            'label' => 'Info',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 0,
        ]);
        MenuItemFactory::createOne([
            'label' => 'Child A',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'news_index',
            'parent' => $parent,
            'position' => 0,
        ]);

        $client->request('GET', '/');
        $html = (string) $client->getResponse()->getContent();

        self::assertStringContainsString('dropdown-toggle', $html);
        self::assertStringContainsString('dropdown-menu', $html);
        self::assertStringContainsString('Child A', $html);
    }
}
