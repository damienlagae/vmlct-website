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

    /**
     * Public-render variant of the stored HTML. Trix only supports a single
     * heading level and emits `<h1>`, which collides with the article title
     * (the real page H1) on the public side — downgrade to `<h2>` for SEO
     * and a11y. Storage stays untouched so the editor keeps round-tripping.
     */
    public function renderedHtml(): string
    {
        return (string) preg_replace(
            ['#<h1(\s|>)#i', '#</h1>#i'],
            ['<h2$1', '</h2>'],
            $this->html,
        );
    }
}
