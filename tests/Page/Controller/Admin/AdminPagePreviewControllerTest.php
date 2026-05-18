<?php

declare(strict_types=1);

namespace App\Tests\Page\Controller\Admin;

use App\Page\Factory\PageFactory;
use App\Shared\Content\Block\TextBlock;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AdminPagePreviewControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testPreviewRequiresAuth(): void
    {
        $client = self::createClient();
        $page = PageFactory::createOne(['path' => 'demo']);

        $client->request('GET', '/admin/pages/'.$page->getId().'/preview');

        self::assertResponseRedirects('/login');
    }

    public function testPreviewRequiresEditPermission(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());
        $page = PageFactory::createOne(['path' => 'demo']);

        $client->request('GET', '/admin/pages/'.$page->getId().'/preview');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testPreviewIncludesDraftContent(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $page = PageFactory::new()->draft()->create([
            'title' => 'Draft page',
            'path' => 'draft-page',
            'content' => [
                (new TextBlock('<p>Hidden draft body.</p>'))->toArray(),
            ],
        ]);

        $client->request('GET', '/admin/pages/'.$page->getId().'/preview');

        self::assertResponseIsSuccessful();
        $html = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('Draft page', $html);
        self::assertStringContainsString('Hidden draft body', $html);
        self::assertStringContainsString('no-store', (string) $client->getResponse()->headers->get('Cache-Control'));
        self::assertSame('SAMEORIGIN', $client->getResponse()->headers->get('X-Frame-Options'));
    }
}
