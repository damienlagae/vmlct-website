<?php

declare(strict_types=1);

namespace App\Shared\Content\Block;

final class HeadingBlock implements BlockInterface
{
    public function __construct(
        public readonly string $text,
        public readonly HeadingLevel $level = HeadingLevel::H2,
    ) {
    }

    public static function type(): string
    {
        return 'heading';
    }

    public static function fromArray(array $data): self
    {
        $level = HeadingLevel::tryFrom((string) ($data['level'] ?? 'h2')) ?? HeadingLevel::H2;

        return new self(
            text: (string) ($data['text'] ?? ''),
            level: $level,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => self::type(),
            'text' => $this->text,
            'level' => $this->level->value,
        ];
    }
}
