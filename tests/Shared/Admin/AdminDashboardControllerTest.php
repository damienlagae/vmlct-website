<?php

declare(strict_types=1);

namespace App\Tests\Shared\Admin;

use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminDashboardControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAdminDashboardRendersShellForAuthenticatedAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorExists('.admin-header');
        self::assertSelectorExists('.admin-sidebar');
        self::assertSelectorExists('.admin-main');
        self::assertSelectorTextContains('.admin-page-header__title', 'Dashboard');
    }

    public function testSidebarHighlightsTheActiveItem(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin');

        $active = $client->getCrawler()->filter('.admin-sidebar__link--active')->text();
        self::assertSame('Dashboard', trim($active));
    }

    public function testHeaderShowsUserDropdown(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create([
            'firstName' => 'Sam',
            'lastName' => 'Tester',
        ]);

        $client->loginUser($admin);
        $client->request('GET', '/admin');

        self::assertSelectorTextContains('.admin-header__user-name', 'Sam Tester');
        self::assertSelectorExists('.dropdown-menu a[href="/profiel"]');
        self::assertSelectorExists('.dropdown-menu a[href="/logout"]');
    }
}
