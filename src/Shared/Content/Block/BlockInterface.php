<?php

declare(strict_types=1);

namespace App\Shared\Content\Block;

/**
 * Contract every content block must implement.
 *
 * The persistence shape is a plain JSON array; conversion to/from
 * a Block instance happens via {@see BlockSerializer} which dispatches
 * on the `type` key.
 */
interface BlockInterface
{
    /**
     * The block discriminator, lowercase (e.g. 'text', 'heading', 'image').
     */
    public static function type(): string;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
