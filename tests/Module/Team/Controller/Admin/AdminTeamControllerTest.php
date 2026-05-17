<?php

declare(strict_types=1);

namespace App\Tests\Module\Team\Controller\Admin;

use App\Module\Team\Entity\RiderCategory;
use App\Module\Team\Entity\StaffRole;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Team\Factory\StaffFactory;
use App\Module\Team\Repository\RiderRepository;
use App\Module\Team\Repository\StaffRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminTeamControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testDashboardShowsStatsForAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        RiderFactory::createMany(3);
        StaffFactory::createMany(2);

        $client->loginUser($admin);
        $client->request('GET', '/admin/team');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('3 renners', $html);
        self::assertStringContainsString('2 omkadering', $html);
    }

    public function testDashboardForbiddenForAnonymous(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/team');

        self::assertResponseRedirects('/login');
    }

    public function testRiderCreatePersistsAndIndexShowsIt(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin/team/renners/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Opslaan', [
            'rider[firstName]' => 'Lance',
            'rider[lastName]' => 'TestRider',
            'rider[dateOfBirth]' => '2010-06-15',
            'rider[category]' => RiderCategory::Aspiranten->value,
            'rider[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/team/renners');

        $repo = self::getContainer()->get(RiderRepository::class);
        $rider = $repo->findOneBy(['lastName' => 'TestRider']);
        self::assertNotNull($rider);
        self::assertSame(RiderCategory::Aspiranten, $rider->getCategory());
    }

    public function testRiderEditUpdatesFields(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $rider = RiderFactory::createOne(['firstName' => 'Old', 'lastName' => 'Name']);

        $client->loginUser($admin);
        $client->request('GET', '/admin/team/renners/'.$rider->getId().'/edit');

        $client->submitForm('Opslaan', [
            'rider[firstName]' => 'New',
            'rider[lastName]' => 'Name',
            'rider[dateOfBirth]' => $rider->getDateOfBirth()->format('Y-m-d'),
            'rider[category]' => $rider->getCategory()->value,
            'rider[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/team/renners');

        $repo = self::getContainer()->get(RiderRepository::class);
        $reloaded = $repo->find($rider->getId());
        self::assertNotNull($reloaded);
        self::assertSame('New', $reloaded->getFirstName());
    }

    public function testRiderDeleteRequiresSuperAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        $rider = RiderFactory::createOne();

        $client->loginUser($admin);
        $client->request('POST', '/admin/team/renners/'.$rider->getId().'/delete', [
            '_token' => 'whatever',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRiderDeleteWorksForSuperAdmin(): void
    {
        $client = self::createClient();
        $superAdmin = UserFactory::new()->superAdmin()->create();
        $rider = RiderFactory::createOne();

        $client->loginUser($superAdmin);
        $client->request('GET', '/admin/team/renners');

        $form = $client->getCrawler()
            ->filter('form[action="/admin/team/renners/'.$rider->getId().'/delete"]')
            ->form();
        $client->submit($form);

        self::assertResponseRedirects('/admin/team/renners');

        $repo = self::getContainer()->get(RiderRepository::class);
        self::assertNull($repo->find($rider->getId()));
    }

    public function testStaffCreateThenEdit(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();

        $client->loginUser($admin);
        $client->request('GET', '/admin/team/staff/new');

        $client->submitForm('Opslaan', [
            'staff[firstName]' => 'Frank',
            'staff[lastName]' => 'CoachTest',
            'staff[role]' => StaffRole::Coach->value,
            'staff[active]' => '1',
        ]);

        self::assertResponseRedirects('/admin/team/staff');

        $repo = self::getContainer()->get(StaffRepository::class);
        $staff = $repo->findOneBy(['lastName' => 'CoachTest']);
        self::assertNotNull($staff);
        self::assertSame(StaffRole::Coach, $staff->getRole());
    }
}
