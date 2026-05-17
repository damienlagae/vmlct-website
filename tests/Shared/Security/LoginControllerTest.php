<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security;

use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class LoginControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testLoginPageRenders(): void
    {
        $client = self::createClient();
        $client->request('GET', '/login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorExists('form input[name="_username"]');
        self::assertSelectorExists('form input[name="_password"]');
        self::assertSelectorExists('form input[name="_csrf_token"]');
    }

    public function testLoginWithBadCredentialsShowsError(): void
    {
        $client = self::createClient();
        UserFactory::new()->withPlainPassword('correct')->create(['email' => 'real@example.com']);

        $client->request('GET', '/login');
        $client->submitForm('Aanmelden', [
            '_username' => 'real@example.com',
            '_password' => 'wrong',
        ]);

        $client->followRedirect();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorExists('.alert.alert-danger');
    }

    public function testAdminWithoutAuthRedirectsToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin');

        self::assertResponseRedirects('/login');
    }

    public function testLoginWithValidCredentialsRedirectsToAdmin(): void
    {
        $client = self::createClient();
        UserFactory::new()->admin()->withPlainPassword('secret')->create([
            'email' => 'admin@example.com',
        ]);

        $client->request('GET', '/login');
        $client->submitForm('Aanmelden', [
            '_username' => 'admin@example.com',
            '_password' => 'secret',
        ]);

        self::assertResponseRedirects('/admin');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testNonAdminCannotAccessAdmin(): void
    {
        $client = self::createClient();
        $user = UserFactory::new()->withPlainPassword('pw')->create([
            'email' => 'user@example.com',
        ]);

        $client->loginUser($user);
        $client->request('GET', '/admin');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
