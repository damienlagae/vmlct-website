<?php

declare(strict_types=1);

namespace App\Tests\Module\Uitslagen\Controller\Admin;

use App\Module\Team\Factory\RiderFactory;
use App\Module\Uitslagen\Entity\ResultStatus;
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
        ResultFactory::createOne([
            'rider' => $rider,
            'raceName' => 'Test race',
            'rank' => 2,
        ]);

        $client->request('GET', '/admin/uitslagen');
        $html = (string) $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Test race', $html);
        self::assertStringContainsString('Tim Demo', $html);
    }

    public function testCreateRequiresRaceNameWhenNoLinkedRace(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $rider = RiderFactory::createOne();

        $crawler = $client->request('GET', '/admin/uitslagen/new');
        $form = $crawler->selectButton('Opslaan')->form();
        $form['result[rider]']->setValue((string) $rider->getId());
        $form['result[status]']->setValue(ResultStatus::Finished->value);
        // raceName left empty AND race left empty → must trigger validation error

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $repo = static::getContainer()->get(ResultRepository::class);
        self::assertSame(0, $repo->count([]));
    }

    public function testCreatePersistsResultWithStandaloneRaceInfo(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $rider = RiderFactory::createOne(['firstName' => 'Tim', 'lastName' => 'Demo']);

        $crawler = $client->request('GET', '/admin/uitslagen/new');
        $form = $crawler->selectButton('Opslaan')->form();
        $form['result[rider]']->setValue((string) $rider->getId());
        $form['result[raceName]']->setValue('Standalone Race');
        $form['result[raceDate]']->setValue((new \DateTimeImmutable('-1 week'))->format('Y-m-d'));
        $form['result[status]']->setValue(ResultStatus::Finished->value);
        $form['result[rank]']->setValue('5');

        $client->submit($form);

        self::assertResponseRedirects('/admin/uitslagen');
        $repo = static::getContainer()->get(ResultRepository::class);
        self::assertSame(1, $repo->count([]));

        $stored = $repo->findOneBy(['raceName' => 'Standalone Race']);
        self::assertNotNull($stored);
        self::assertSame(5, $stored->getRank());
        self::assertSame('Standalone Race', $stored->getEffectiveRaceName());
    }

    public function testDeleteRemovesResult(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->superAdmin()->create());

        $result = ResultFactory::createOne(['raceName' => 'Doomed race']);
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
