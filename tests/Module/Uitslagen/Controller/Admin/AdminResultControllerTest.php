<?php

declare(strict_types=1);

namespace App\Tests\Module\Uitslagen\Controller\Admin;

use App\Module\Programme\Factory\RaceFactory;
use App\Module\Programme\Factory\RaceStageFactory;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Factory\ResultFactory;
use App\Module\Uitslagen\Repository\ResultRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminResultControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexRedirectsAnonymous(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/uitslagen');

        self::assertResponseRedirects('/login');
    }

    public function testIndexForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());

        $client->request('GET', '/admin/uitslagen');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexListsResults(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $rider = RiderFactory::createOne(['firstName' => 'Tim', 'lastName' => 'Demo']);
        $race = RaceFactory::new()->past()->create(['name' => 'Test race']);
        $stage = RaceStageFactory::createOne(['race' => $race, 'position' => 1]);
        ResultFactory::createOne(['rider' => $rider, 'stage' => $stage, 'rank' => 2]);

        $client->request('GET', '/admin/uitslagen');
        $html = (string) $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Test race', $html);
        self::assertStringContainsString('Tim Demo', $html);
    }

    public function testDeleteRemovesResult(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->superAdmin()->create());

        $race = RaceFactory::new()->past()->create(['name' => 'Doomed race']);
        $stage = RaceStageFactory::createOne(['race' => $race, 'position' => 1]);
        $rider = RiderFactory::createOne();
        $result = ResultFactory::createOne(['rider' => $rider, 'stage' => $stage, 'rank' => 1]);
        $id = $result->getId();

        $client->request('GET', '/admin/uitslagen');
        $form = $client->getCrawler()
            ->filter('form[action="/admin/uitslagen/'.$id.'/delete"]')
            ->form()
        ;
        $client->submit($form);

        self::assertResponseRedirects('/admin/uitslagen');
        $repo = static::getContainer()->get(ResultRepository::class);
        self::assertNull($repo->find($id));
    }
}
