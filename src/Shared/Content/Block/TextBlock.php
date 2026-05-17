<?php

declare(strict_types=1);

namespace App\Shared\Content\Block;

final class TextBlock implements BlockInterface
{
    public function __construct(
        public readonly string $html,
    ) {
    }

    public static function type(): string
    {
        return 'text';
    }

    public static function fromArray(array $data): self
    {
        return new self((string) ($data['html'] ?? ''));
    }

    public function toArray(): array
    {
        return [
            'type' => self::type(),
            'html' => $this->html,
        ];
    }
}
