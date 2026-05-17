<?php

declare(strict_types=1);

namespace App\Shared\Content;

use App\Shared\Content\Block\BlockInterface;
use App\Shared\Content\Block\HeadingBlock;
use App\Shared\Content\Block\ImageBlock;
use App\Shared\Content\Block\TextBlock;

/**
 * Converts the raw JSON content stored on Article (and later Page) back and
 * forth between plain arrays and BlockInterface objects.
 *
 * Adding a new block type means registering it in {@see self::REGISTRY}.
 */
final class BlockSerializer
{
    /**
     * @var array<string, class-string<BlockInterface>>
     */
    private const REGISTRY = [
        'text' => TextBlock::class,
        'heading' => HeadingBlock::class,
        'image' => ImageBlock::class,
    ];

    /**
     * @param array<int, array<string, mixed>> $raw
     *
     * @return list<BlockInterface>
     */
    public function deserialize(array $raw): array
    {
        $blocks = [];
        foreach ($raw as $payload) {
            if (!\is_array($payload)) {
                continue;
            }
            $type = $payload['type'] ?? null;
            if (!\is_string($type) || !isset(self::REGISTRY[$type])) {
                continue;
            }
            $class = self::REGISTRY[$type];
            $blocks[] = $class::fromArray($payload);
        }

        return $blocks;
    }

    /**
     * @param list<BlockInterface> $blocks
     *
     * @return array<int, array<string, mixed>>
     */
    public function serialize(array $blocks): array
    {
        return array_map(static fn (BlockInterface $block): array => $block->toArray(), $blocks);
    }
}
