<?php

declare(strict_types=1);

namespace App\Shared\Media;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Persists an uploaded file to the configured Flysystem storage.
 *
 * Each module is expected to upload into its own subdirectory
 * (e.g. 'sponsor', 'rider', 'staff'). The returned path is what should
 * be stored on the entity (relative to the storage root); the LiipImagine
 * filters use it as-is to resolve the variants.
 */
final class Uploader
{
    public function __construct(
        private readonly FilesystemOperator $defaultStorage,
        private readonly SluggerInterface $slugger,
    ) {
    }

    /**
     * @return string the relative path within the default storage
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = $this->slugger->slug($original)->lower();
        $extension = $file->guessExtension();
        if (null === $extension || '' === $extension) {
            $extension = $file->getClientOriginalExtension() ?: 'bin';
        }
        $filename = sprintf('%s-%s.%s', $slug, bin2hex(random_bytes(4)), $extension);

        $path = trim($directory, '/').'/'.$filename;

        $stream = fopen($file->getPathname(), 'rb');
        if (false === $stream) {
            throw new UploadException('Unable to open uploaded file for reading.');
        }

        try {
            $this->defaultStorage->writeStream($path, $stream);
        } finally {
            if (\is_resource($stream)) {
                fclose($stream);
            }
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (null === $path || '' === $path) {
            return;
        }

        if ($this->defaultStorage->fileExists($path)) {
            $this->defaultStorage->delete($path);
        }
    }
}
