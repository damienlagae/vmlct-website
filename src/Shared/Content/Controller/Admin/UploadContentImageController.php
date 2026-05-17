<?php

declare(strict_types=1);

namespace App\Shared\Content\Controller\Admin;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Inline upload endpoint for content blocks (Article, future Page).
 *
 * Receives a single file via `multipart/form-data`, validates it as a
 * web-safe image, persists it through the `default.storage` Flysystem
 * filesystem under `content/<ulid>.<ext>`, and returns the stored path
 * so the caller (Live Component editor) can drop it into a block's
 * `path` field for later rendering through Liip Imagine.
 */
#[AsController]
#[IsGranted('ROLE_ADMIN')]
final class UploadContentImageController
{
    private const MAX_BYTES = 5 * 1024 * 1024;

    public function __construct(
        private readonly FilesystemOperator $defaultStorage,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/admin/content/upload-image', name: 'admin_content_upload_image', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (null === $file) {
            throw new BadRequestHttpException('No file uploaded.');
        }

        $violations = $this->validator->validate(
            $file,
            new Assert\Image(
                maxSize: self::MAX_BYTES,
                mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],
            ),
        );

        if (\count($violations) > 0) {
            $first = $violations->get(0);

            return new JsonResponse(
                ['error' => $first->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $extension = strtolower($file->guessExtension() ?? $file->getClientOriginalExtension());
        if ('' === $extension) {
            $extension = 'bin';
        }

        $relativePath = \sprintf('content/%s.%s', new Ulid(), $extension);

        $stream = fopen($file->getRealPath(), 'rb');
        if (false === $stream) {
            throw new BadRequestHttpException('Unable to read uploaded file.');
        }

        try {
            $this->defaultStorage->writeStream($relativePath, $stream);
        } finally {
            if (\is_resource($stream)) {
                fclose($stream);
            }
        }

        return new JsonResponse(['path' => $relativePath], Response::HTTP_CREATED);
    }
}
