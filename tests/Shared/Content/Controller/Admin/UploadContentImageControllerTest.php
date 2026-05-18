<?php

declare(strict_types=1);

namespace App\Tests\Shared\Content\Controller\Admin;

use App\Shared\Security\Factory\UserFactory;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UploadContentImageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUnauthenticatedUploadIsRejected(): void
    {
        $client = self::createClient();
        $client->request('POST', '/admin/content/upload-image');

        self::assertResponseRedirects('/login');
    }

    public function testNonAdminUploadIsForbidden(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::createOne());

        $client->request('POST', '/admin/content/upload-image');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testEmptyUploadReturnsBadRequest(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', '/admin/content/upload-image');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testNonImageFileIsRejected(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $tmp = tempnam(sys_get_temp_dir(), 'upl');
        self::assertNotFalse($tmp);
        file_put_contents($tmp, 'not an image');
        $upload = new UploadedFile($tmp, 'bogus.txt', 'text/plain', test: true);

        $client->request('POST', '/admin/content/upload-image', [], ['file' => $upload]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testValidImageIsStoredAndPathReturned(): void
    {
        $client = self::createClient();
        $client->loginUser(UserFactory::new()->admin()->create());

        $upload = new UploadedFile($this->makePngFixture(), 'pic.png', 'image/png', test: true);

        $client->request('POST', '/admin/content/upload-image', [], ['file' => $upload]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = json_decode((string) $client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
        self::assertArrayHasKey('path', $data);
        self::assertStringStartsWith('content/', $data['path']);
        self::assertStringEndsWith('.png', $data['path']);

        /** @var FilesystemOperator $storage */
        $storage = static::getContainer()->get('default.storage');
        self::assertTrue($storage->fileExists($data['path']));

        $storage->delete($data['path']);
    }

    private function makePngFixture(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'png');
        self::assertNotFalse($tmp);

        // 1x1 transparent PNG — hardcoded so the test stays independent
        // from the GD extension (which we still need in prod for Liip).
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
            true,
        );
        self::assertNotFalse($png);
        file_put_contents($tmp, $png);

        return $tmp;
    }
}
