<?php

declare(strict_types=1);

namespace App\Tests\Module\Menu\Controller\Admin;

use App\Module\Menu\Entity\MenuTargetType;
use App\Module\Menu\Factory\MenuItemFactory;
use App\Module\Menu\Repository\MenuItemRepository;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminMenuControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndexRedirectsAnonymousToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/menu');

        self::assertResponseRedirects('/login');
    }

    public function testIndexForbiddenForRegularUser(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());

        $client->request('GET', '/admin/menu');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testIndexListsRootsAndChildren(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $root = MenuItemFactory::createOne(['label' => 'Hoofd', 'position' => 0]);
        MenuItemFactory::createOne(['label' => 'Kind', 'parent' => $root, 'position' => 0]);

        $client->request('GET', '/admin/menu');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Hoofd', $html);
        self::assertStringContainsString('Kind', $html);
    }

    public function testCreateRouteItemPersists(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', '/admin/menu/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Opslaan')->form();
        $form['menu_item[label]']->setValue('Nieuws');
        $form['menu_item[targetType]']->setValue(MenuTargetType::Route->value);
        $form['menu_item[routeName]']->setValue('news_index');
        $form['menu_item[position]']->setValue('5');
        $form['menu_item[active]']->tick();

        $client->submit($form);
        self::assertResponseRedirects('/admin/menu');

        $repo = static::getContainer()->get(MenuItemRepository::class);
        $stored = $repo->findOneBy(['label' => 'Nieuws']);
        self::assertNotNull($stored);
        self::assertSame('news_index', $stored->getRouteName());
        self::assertSame(MenuTargetType::Route, $stored->getTargetType());
    }

    public function testCreateRouteItemWithoutRouteFailsValidation(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $crawler = $client->request('GET', '/admin/menu/new');
        $form = $crawler->selectButton('Opslaan')->form();
        $form['menu_item[label]']->setValue('Broken');
        $form['menu_item[targetType]']->setValue(MenuTargetType::Route->value);
        // routeName left empty
        $form['menu_item[position]']->setValue('0');

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $repo = static::getContainer()->get(MenuItemRepository::class);
        self::assertNull($repo->findOneBy(['label' => 'Broken']));
    }

    public function testDeleteRemovesItem(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->superAdmin()->create());

        $item = MenuItemFactory::createOne(['label' => 'Doomed']);
        $id = $item->getId();

        $client->request('GET', '/admin/menu');
        $form = $client->getCrawler()
            ->filter('form[action="/admin/menu/'.$id.'/delete"]')
            ->form()
        ;
        $client->submit($form);

        self::assertResponseRedirects('/admin/menu');
        $repo = static::getContainer()->get(MenuItemRepository::class);
        self::assertNull($repo->find($id));
    }
}
