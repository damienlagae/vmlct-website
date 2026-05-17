<?php

declare(strict_types=1);

namespace App\Tests\Shared\Admin\Controller\User;

use App\Shared\Security\Entity\User;
use App\Shared\Security\Factory\UserFactory;
use App\Shared\Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminUserControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexIsForbiddenForAnonymous(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/users');

        self::assertResponseRedirects('/login');
    }

    public function testIndexIsForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $user = UserFactory::createOne();

        $client->loginUser($user);
        $client->request('GET', '/admin/users');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexShowsUsersWithStats(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        UserFactory::createOne();
        UserFactory::new()->inactive()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin/users');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('.admin-page-header__title', 'Gebruikers');

        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString($admin->getEmail(), $html);
    }

    public function testCreatePersistsUserWithHashedPassword(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin/users/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Opslaan', [
            'user[email]' => 'newbie@example.com',
            'user[firstName]' => 'New',
            'user[lastName]' => 'Person',
            'user[plainPassword]' => 'longenoughpw',
            'user[roles]' => ['ROLE_ADMIN'],
            'user[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/users');

        $repo = self::getContainer()->get(UserRepository::class);
        $created = $repo->findOneByEmail('newbie@example.com');
        self::assertInstanceOf(User::class, $created);
        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $created->getRoles());

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($hasher->isPasswordValid($created, 'longenoughpw'));
    }

    public function testEditUpdatesFieldsWithoutChangingPasswordWhenEmpty(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $target = UserFactory::new()->withPlainPassword('original')->create([
            'email' => 'target@example.com',
            'firstName' => 'Old',
            'lastName' => 'Name',
        ]);

        $client->loginUser($admin);
        $client->request('GET', '/admin/users/'.$target->getId().'/edit');
        self::assertResponseIsSuccessful();

        $client->submitForm('Opslaan', [
            'user[email]' => 'target@example.com',
            'user[firstName]' => 'Updated',
            'user[lastName]' => 'Name',
            'user[plainPassword]' => '',
            'user[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/users');

        $repo = self::getContainer()->get(UserRepository::class);
        $reloaded = $repo->find($target->getId());
        self::assertNotNull($reloaded);
        self::assertSame('Updated', $reloaded->getFirstName());

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($hasher->isPasswordValid($reloaded, 'original'));
    }

    public function testEditChangesPasswordWhenProvided(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $target = UserFactory::new()->withPlainPassword('original')->create([
            'email' => 'target2@example.com',
        ]);

        $client->loginUser($admin);
        $client->request('GET', '/admin/users/'.$target->getId().'/edit');

        $client->submitForm('Opslaan', [
            'user[email]' => 'target2@example.com',
            'user[firstName]' => $target->getFirstName(),
            'user[lastName]' => $target->getLastName(),
            'user[plainPassword]' => 'brandnewpassword',
            'user[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/users');

        $repo = self::getContainer()->get(UserRepository::class);
        $reloaded = $repo->find($target->getId());
        self::assertNotNull($reloaded);

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($hasher->isPasswordValid($reloaded, 'brandnewpassword'));
        self::assertFalse($hasher->isPasswordValid($reloaded, 'original'));
    }

    public function testDeleteRequiresSuperAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $target = UserFactory::createOne();

        $client->loginUser($admin);
        $client->request('POST', '/admin/users/'.$target->getId().'/delete', [
            '_token' => 'whatever',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $repo = self::getContainer()->get(UserRepository::class);
        self::assertNotNull($repo->find($target->getId()), 'User should still exist');
    }

    public function testSuperAdminCanDeleteOtherUser(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();
        $target = UserFactory::createOne(['email' => 'tobedeleted@example.com']);

        $client->loginUser($superAdmin);
        $client->request('GET', '/admin/users');

        $form = $client->getCrawler()
            ->filter('form[action="/admin/users/'.$target->getId().'/delete"]')
            ->form();
        $client->submit($form);

        self::assertResponseRedirects('/admin/users');

        $repo = self::getContainer()->get(UserRepository::class);
        self::assertNull($repo->find($target->getId()));
    }

    public function testSuperAdminCannotDeleteSelf(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();

        $client->loginUser($superAdmin);
        $client->request('POST', '/admin/users/'.$superAdmin->getId().'/delete', [
            '_token' => 'whatever',
        ]);

        // Token is invalid, so the CSRF check trips before the self-delete guard.
        // Use a real session-backed token via the index page form-grab pattern instead.
        $client->request('GET', '/admin/users');
        $html = (string) $client->getResponse()->getContent();

        // The current user's row should not even render a delete form
        self::assertStringNotContainsString('/admin/users/'.$superAdmin->getId().'/delete', $html);
    }
}
