<?php

declare(strict_types=1);

namespace App\Tests\Module\Programme\Controller\Admin;

use App\Module\Programme\Entity\RaceDiscipline;
use App\Module\Programme\Factory\RaceFactory;
use App\Module\Programme\Repository\RaceRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminRaceControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexRedirectsAnonymousToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/programme');

        self::assertResponseRedirects('/login');
    }

    public function testIndexForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());

        $client->request('GET', '/admin/programme');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexListsRacesGroupedByTime(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        RaceFactory::new()->upcoming()->create(['name' => 'Future race']);
        RaceFactory::new()->past()->create(['name' => 'Past race']);

        $client->request('GET', '/admin/programme');
        $html = (string) $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Future race', $html);
        self::assertStringContainsString('Past race', $html);
    }

    public function testCreatePersistsARace(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', '/admin/programme/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Opslaan')->form();
        $form['race[name]']->setValue('Heuvelse Pijl');
        $form['race[startDate]']->setValue((new \DateTimeImmutable('+1 month'))->format('Y-m-d'));
        $form['race[location]']->setValue('Beveren');
        $form['race[discipline]']->setValue(RaceDiscipline::Road->value);
        $form['race[categories][2]']->tick(); // 3rd checkbox = nieuwelingen

        $client->submit($form);

        self::assertResponseRedirects('/admin/programme');

        $repo = static::getContainer()->get(RaceRepository::class);
        $stored = $repo->findOneBy(['name' => 'Heuvelse Pijl']);
        self::assertNotNull($stored);
        self::assertSame('Beveren', $stored->getLocation());
        self::assertSame(RaceDiscipline::Road, $stored->getDiscipline());
        self::assertCount(1, $stored->getCategories());
    }

    public function testDeleteRemovesRace(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->superAdmin()->create());

        $race = RaceFactory::createOne(['name' => 'Doomed']);
        $id = $race->getId();

        $client->request('GET', '/admin/programme');
        $form = $client->getCrawler()
            ->filter('form[action="/admin/programme/'.$id.'/delete"]')
            ->form()
        ;
        $client->submit($form);

        self::assertResponseRedirects('/admin/programme');
        $repo = static::getContainer()->get(RaceRepository::class);
        self::assertNull($repo->find($id));
    }
}
