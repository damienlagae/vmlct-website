<?php

declare(strict_types=1);

namespace App\Shared\Content\Block;

final class ImageBlock implements BlockInterface
{
    public function __construct(
        public readonly string $path,
        public readonly string $alt = '',
        public readonly ?string $caption = null,
        public readonly ImageAlignment $alignment = ImageAlignment::Full,
    ) {
    }

    public static function type(): string
    {
        return 'image';
    }

    public static function fromArray(array $data): self
    {
        $alignment = ImageAlignment::tryFrom((string) ($data['alignment'] ?? 'full')) ?? ImageAlignment::Full;
        $caption = $data['caption'] ?? null;

        return new self(
            path: (string) ($data['path'] ?? ''),
            alt: (string) ($data['alt'] ?? ''),
            caption: \is_string($caption) && '' !== $caption ? $caption : null,
            alignment: $alignment,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => self::type(),
            'path' => $this->path,
            'alt' => $this->alt,
            'caption' => $this->caption,
            'alignment' => $this->alignment->value,
        ];
    }
}
