<?php

declare(strict_types=1);

namespace App\Tests\Module\Sponsor\Controller\Admin;

use App\Module\Sponsor\Entity\Sponsor;
use App\Module\Sponsor\Factory\SponsorFactory;
use App\Module\Sponsor\Repository\SponsorRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminSponsorControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexIsForbiddenForAnonymous(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/sponsors');

        self::assertResponseRedirects('/login');
    }

    public function testIndexIsForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $user = UserFactory::createOne();

        $client->loginUser($user);
        $client->request('GET', '/admin/sponsors');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexShowsSponsorsAndStats(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        SponsorFactory::createOne(['name' => 'Alpha Active', 'active' => true, 'displayOrder' => 1]);
        SponsorFactory::createOne(['name' => 'Beta Active', 'active' => true, 'displayOrder' => 2]);
        SponsorFactory::createOne(['name' => 'Gamma Hidden', 'active' => false, 'displayOrder' => 3]);

        $client->loginUser($admin);
        $client->request('GET', '/admin/sponsors');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSelectorTextContains('.admin-page-header__title', 'Sponsors');

        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Alpha Active', $html);
        self::assertStringContainsString('Beta Active', $html);
        self::assertStringContainsString('Gamma Hidden', $html);
    }

    public function testCreateRendersFormThenPersistsSponsor(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin/sponsors/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Opslaan', [
            'sponsor[name]' => 'New Sponsor',
            'sponsor[websiteUrl]' => 'https://example.com',
            'sponsor[displayOrder]' => '5',
            'sponsor[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/sponsors');

        $sponsor = self::getContainer()->get(SponsorRepository::class)->findOneBy(['name' => 'New Sponsor']);
        self::assertInstanceOf(Sponsor::class, $sponsor);
        self::assertSame('https://example.com', $sponsor->getWebsiteUrl());
        self::assertSame(5, $sponsor->getDisplayOrder());
        self::assertTrue($sponsor->isActive());
    }

    public function testEditUpdatesExistingSponsor(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $sponsor = SponsorFactory::createOne(['name' => 'Old Name', 'displayOrder' => 1]);

        $client->loginUser($admin);
        $client->request('GET', '/admin/sponsors/'.$sponsor->getId().'/edit');
        self::assertResponseIsSuccessful();

        $client->submitForm('Opslaan', [
            'sponsor[name]' => 'New Name',
            'sponsor[displayOrder]' => '42',
        ]);

        self::assertResponseRedirects('/admin/sponsors');

        $reloaded = self::getContainer()->get(SponsorRepository::class)->find($sponsor->getId());
        self::assertNotNull($reloaded);
        self::assertSame('New Name', $reloaded->getName());
        self::assertSame(42, $reloaded->getDisplayOrder());
    }

    public function testDeleteRequiresSuperAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $sponsor = SponsorFactory::createOne();

        $client->loginUser($admin);
        $client->request('POST', '/admin/sponsors/'.$sponsor->getId().'/delete', [
            '_token' => 'whatever',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $repo = self::getContainer()->get(SponsorRepository::class);
        self::assertNotNull($repo->find($sponsor->getId()), 'Sponsor should not have been deleted');
    }

    public function testDeleteRemovesSponsorWhenSuperAdminWithValidCsrf(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();
        $sponsor = SponsorFactory::createOne();

        $client->loginUser($superAdmin);
        // First fetch a page so we can submit a real CSRF token tied to the session
        $client->request('GET', '/admin/sponsors');

        $form = $client->getCrawler()
            ->filter('form[action="/admin/sponsors/'.$sponsor->getId().'/delete"]')
            ->form();
        $client->submit($form);

        self::assertResponseRedirects('/admin/sponsors');

        $repo = self::getContainer()->get(SponsorRepository::class);
        self::assertNull($repo->find($sponsor->getId()), 'Sponsor should have been deleted');
    }

    public function testDeleteFailsWithInvalidCsrf(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();
        $sponsor = SponsorFactory::createOne();

        $client->loginUser($superAdmin);
        $client->request('POST', '/admin/sponsors/'.$sponsor->getId().'/delete', [
            '_token' => 'not-the-real-token',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
