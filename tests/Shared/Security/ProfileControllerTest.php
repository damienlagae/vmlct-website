<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security;

use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProfileControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAnonymousIsRedirectedToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/profiel');

        self::assertResponseRedirects('/login');
    }

    public function testRegularUserSeesViewPermissionOnly(): void
    {
        $client = self::createClient();
        $user = UserFactory::createOne();

        $client->loginUser($user);
        $client->request('GET', '/profiel');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('.container > h1', 'Mijn profiel');

        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('SPONSOR_VIEW', $html);
        self::assertStringNotContainsString('SPONSOR_CREATE', $html);
        self::assertStringNotContainsString('SPONSOR_DELETE', $html);
    }

    public function testAdminSeesViewCreateEditButNotDelete(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/profiel');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('SPONSOR_VIEW', $html);
        self::assertStringContainsString('SPONSOR_CREATE', $html);
        self::assertStringContainsString('SPONSOR_EDIT', $html);
        self::assertStringNotContainsString('SPONSOR_DELETE', $html);
    }

    public function testSuperAdminSeesAllPermissions(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();

        $client->loginUser($superAdmin);
        $client->request('GET', '/profiel');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('SPONSOR_VIEW', $html);
        self::assertStringContainsString('SPONSOR_CREATE', $html);
        self::assertStringContainsString('SPONSOR_EDIT', $html);
        self::assertStringContainsString('SPONSOR_DELETE', $html);
    }

    public function testProfileShowsUserInfo(): void
    {
        $client = self::createClient();
        $user = UserFactory::new()->admin()->create([
            'email' => 'damien@example.com',
            'firstName' => 'Damien',
            'lastName' => 'Lagae',
        ]);

        $client->loginUser($user);
        $client->request('GET', '/profiel');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('damien@example.com', $html);
        self::assertStringContainsString('Damien Lagae', $html);
        self::assertStringContainsString('ROLE_ADMIN', $html);
    }
}
